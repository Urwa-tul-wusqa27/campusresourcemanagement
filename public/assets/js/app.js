document.addEventListener('DOMContentLoaded', () => {
  const calendarEl = document.getElementById('calendar');
  const typeFilter = document.getElementById('resourceTypeFilter');
  const resourceFilter = document.getElementById('resourceFilter');

  if (calendarEl && window.FullCalendar) {
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      height: 'auto',
      nowIndicator: true,
      selectable: true,
      slotMinTime: '08:00:00',
      slotMaxTime: '16:00:00',
      businessHours: {
        daysOfWeek: [1, 2, 3, 4, 5],
        startTime: '08:00',
        endTime: '16:00'
      },
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events(fetchInfo, successCallback, failureCallback) {
        const params = new URLSearchParams();
        params.set('start', fetchInfo.startStr);
        params.set('end', fetchInfo.endStr);
        if (typeFilter && typeFilter.value) params.set('type', typeFilter.value);
        if (resourceFilter && resourceFilter.value) params.set('resource_id', resourceFilter.value);

        fetch(`/api/events.php?${params.toString()}`)
          .then((response) => response.json())
          .then(successCallback)
          .catch(failureCallback);
      },
      eventDidMount(info) {
        const status = info.event.extendedProps.status;
        info.el.title = `${info.event.title} (${status})`;
      }
    });

    calendar.render();

    const refreshCalendar = () => calendar.refetchEvents();
    typeFilter?.addEventListener('change', () => {
      const selectedType = typeFilter.value;
      [...resourceFilter.options].forEach((option) => {
        if (!option.value) return;
        option.hidden = selectedType && option.dataset.type !== selectedType;
      });
      resourceFilter.value = '';
      refreshCalendar();
    });
    resourceFilter?.addEventListener('change', refreshCalendar);
  }

  const bookingForm = document.getElementById('bookingForm');
  if (bookingForm) {
    const resource = document.getElementById('bookingResource');
    const start = document.getElementById('startTime');
    const end = document.getElementById('endTime');
    const result = document.getElementById('conflictResult');
    const eventName = document.getElementById('eventName');
    const participants = document.getElementById('participants');
    const capacityHint = document.getElementById('capacityHint');
    const previewInitial = document.getElementById('previewInitial');
    const previewType = document.getElementById('previewType');
    const previewName = document.getElementById('previewName');
    const previewFeatures = document.getElementById('previewFeatures');
    const summaryTitle = document.getElementById('summaryTitle');
    const summaryTime = document.getElementById('summaryTime');
    const summaryPeople = document.getElementById('summaryPeople');

    const selectedResource = () => resource?.selectedOptions?.[0] || null;

    const formatDateTime = (value) => {
      if (!value) return '';
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return '';

      return date.toLocaleString([], {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
      });
    };

    const updateResourcePreview = () => {
      const option = selectedResource();
      const hasResource = option && option.value;
      const type = hasResource ? option.dataset.type : 'Resource';
      const name = hasResource ? option.dataset.name : 'Select a resource';
      const capacity = hasResource ? option.dataset.capacity : '';
      const features = hasResource ? option.dataset.features : 'Capacity and features will appear here.';

      if (previewInitial) {
        previewInitial.textContent = hasResource
          ? name.split(/\s+/).slice(0, 2).map((part) => part[0]).join('').toUpperCase()
          : 'CB';
      }

      if (previewType) {
        previewType.className = hasResource ? `type-pill type-${type}` : 'type-pill';
        previewType.textContent = hasResource ? type.charAt(0).toUpperCase() + type.slice(1) : 'Resource';
      }

      if (previewName) previewName.textContent = name;
      if (previewFeatures) {
        previewFeatures.textContent = hasResource
          ? `Capacity ${capacity || 0} - ${features || 'No listed features'}`
          : features;
      }
    };

    const updateCapacityHint = () => {
      if (!capacityHint || !participants) return;
      const option = selectedResource();
      const capacity = Number(option?.dataset.capacity || 0);
      const people = Number(participants.value || 0);

      if (!option?.value || !people) {
        capacityHint.textContent = '';
        capacityHint.className = 'capacity-hint';
        return;
      }

      if (capacity > 0 && people > capacity) {
        capacityHint.textContent = `Capacity warning: this resource is listed for ${capacity} people.`;
        capacityHint.className = 'capacity-hint warning-text';
      } else {
        capacityHint.textContent = capacity > 0
          ? `${capacity - people} seats remain based on listed capacity.`
          : 'No fixed capacity limit is listed for this resource.';
        capacityHint.className = 'capacity-hint success-text';
      }
    };

    const updateSummary = () => {
      if (summaryTitle) summaryTitle.textContent = eventName?.value.trim() || 'Untitled event';
      if (summaryTime) {
        const from = formatDateTime(start?.value);
        const to = formatDateTime(end?.value);
        summaryTime.textContent = from && to ? `${from} to ${to}` : 'No time selected';
      }
      if (summaryPeople) {
        const count = Number(participants?.value || 1);
        summaryPeople.textContent = `${count} participant${count === 1 ? '' : 's'}`;
      }
    };

    const setDuration = (minutes) => {
      if (!start.value) {
        const now = new Date();
        now.setMinutes(Math.ceil(now.getMinutes() / 15) * 15, 0, 0);
        if (now.getHours() < 8) now.setHours(8, 0, 0, 0);
        if (now.getHours() >= 16) {
          now.setDate(now.getDate() + 1);
          now.setHours(8, 0, 0, 0);
        }
        start.value = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
      }

      const startDate = new Date(start.value);
      if (Number.isNaN(startDate.getTime())) return;

      const endDate = new Date(startDate.getTime() + minutes * 60000);
      if (endDate.getHours() > 16 || (endDate.getHours() === 16 && endDate.getMinutes() > 0)) {
        endDate.setHours(16, 0, 0, 0);
      }
      end.value = new Date(endDate.getTime() - endDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
      updateSummary();
      checkConflict();
    };

    const checkConflict = () => {
      if (!resource.value || !start.value || !end.value) {
        if (result) {
          result.className = 'inline-message availability-message';
          result.textContent = 'Choose a resource and time to check availability.';
        }
        return;
      }

      result.className = 'inline-message availability-message checking';
      result.textContent = 'Checking availability...';

      const params = new URLSearchParams({
        resource_id: resource.value,
        start_time: start.value,
        end_time: end.value
      });

      fetch(`/api/check_conflict.php?${params.toString()}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            result.textContent = '';
            return;
          }

          result.className = data.conflict ? 'inline-message error-text' : 'inline-message success-text';
          result.textContent = data.conflict
            ? 'Conflict found. Submit to see available slot suggestions.'
            : 'This slot is currently available.';
        });
    };

    resource?.addEventListener('change', () => {
      updateResourcePreview();
      updateCapacityHint();
      updateSummary();
      checkConflict();
    });

    [start, end].forEach((field) => field?.addEventListener('change', () => {
      updateSummary();
      checkConflict();
    }));

    eventName?.addEventListener('input', updateSummary);
    participants?.addEventListener('input', () => {
      updateCapacityHint();
      updateSummary();
    });

    document.querySelectorAll('.duration-chip').forEach((button) => {
      button.addEventListener('click', () => setDuration(Number(button.dataset.minutes || 60)));
    });

    document.querySelectorAll('.slot-button').forEach((button) => {
      button.addEventListener('click', () => {
        start.value = button.dataset.start;
        end.value = button.dataset.end;
        updateSummary();
        checkConflict();
      });
    });

    updateResourcePreview();
    updateCapacityHint();
    updateSummary();
    checkConflict();
  }
});
