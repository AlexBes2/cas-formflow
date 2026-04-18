(function () {
	'use strict';

	var FIELD_SELECTOR = 'input, select, textarea';
	var STEP_ERROR_MESSAGE = 'Please fix the highlighted fields before continuing.';

	function getFocusableField(step) {
		return step.querySelector(FIELD_SELECTOR + ', button');
	}

	function getFieldWrapper(field) {
		return field.closest('.cas-formflow-field, .cas-formflow-checkbox');
	}

	function getFieldErrorId(field) {
		return field.id ? field.id + '-error' : '';
	}

	function getDescribedByIds(field) {
		var describedBy = field.getAttribute('aria-describedby');

		return describedBy ? describedBy.split(/\s+/).filter(Boolean) : [];
	}

	function addDescribedById(field, id) {
		var ids = getDescribedByIds(field);

		if (ids.indexOf(id) === -1) {
			ids.push(id);
		}

		field.setAttribute('aria-describedby', ids.join(' '));
	}

	function removeDescribedById(field, id) {
		var ids = getDescribedByIds(field).filter(function (describedById) {
			return describedById !== id;
		});

		if (ids.length) {
			field.setAttribute('aria-describedby', ids.join(' '));
		} else {
			field.removeAttribute('aria-describedby');
		}
	}

	function hasFieldValue(field) {
		if (field.type === 'checkbox' || field.type === 'radio') {
			return field.checked;
		}

		return field.value.trim() !== '';
	}

	function getFieldMessage(field, messageType, fallback) {
		var customMessage = field.getAttribute('data-error-' + messageType);

		return customMessage || fallback;
	}

	function getFieldErrorMessage(field) {
		var validity = field.validity;

		if (validity.valid) {
			return '';
		}

		if (validity.valueMissing) {
			return getFieldMessage(field, 'required', 'This field is required.');
		}

		if (validity.typeMismatch) {
			return getFieldMessage(field, 'type', 'Enter a valid value.');
		}

		if (validity.patternMismatch) {
			return getFieldMessage(field, 'pattern', 'Use the requested format.');
		}

		if (validity.tooShort) {
			return getFieldMessage(field, 'minlength', 'Enter a longer value.');
		}

		if (validity.tooLong) {
			return getFieldMessage(field, 'maxlength', 'Enter a shorter value.');
		}

		if (validity.rangeUnderflow) {
			return getFieldMessage(field, 'min', 'Enter a higher value.');
		}

		if (validity.rangeOverflow) {
			return getFieldMessage(field, 'max', 'Enter a lower value.');
		}

		return getFieldMessage(field, 'invalid', 'Enter a valid value.');
	}

	function setFieldError(field, message) {
		var wrapper = getFieldWrapper(field);
		var errorId = getFieldErrorId(field);
		var error = wrapper && errorId ? wrapper.querySelector('#' + errorId) : null;
		var isValidated = field.getAttribute('data-cas-validated') === 'true';
		var hasValue = hasFieldValue(field);

		field.classList.toggle('is-invalid', Boolean(message));
		field.classList.toggle('is-valid', !message && isValidated && hasValue);
		field.setAttribute('aria-invalid', message ? 'true' : 'false');

		if (wrapper) {
			wrapper.classList.toggle('has-error', Boolean(message));
			wrapper.classList.toggle('has-success', !message && isValidated && hasValue);
		}

		if (!wrapper || !errorId) {
			return;
		}

		if (!error) {
			error = document.createElement('p');
			error.className = 'cas-formflow-error invalid-feedback mb-0';
			error.id = errorId;
			wrapper.appendChild(error);
		}

		error.textContent = message;
		error.hidden = !message;

		if (message) {
			addDescribedById(field, errorId);
		} else {
			removeDescribedById(field, errorId);
		}
	}

	function validateField(field) {
		var message = getFieldErrorMessage(field);

		field.setAttribute('data-cas-validated', 'true');
		setFieldError(field, message);

		return !message;
	}

	function getStepFields(step) {
		return Array.prototype.slice.call(step.querySelectorAll(FIELD_SELECTOR));
	}

	function setStepAlert(step, invalidCount) {
		var alert = step.querySelector('.cas-formflow-step-alert');

		if (!alert) {
			return;
		}

		if (invalidCount > 0) {
			alert.textContent = STEP_ERROR_MESSAGE;
			alert.hidden = false;
			return;
		}

		alert.textContent = '';
		alert.hidden = true;
	}

	function updateProgressErrorState(progressItems, stepIndex, hasError) {
		var progressItem = progressItems[stepIndex];

		if (!progressItem) {
			return;
		}

		progressItem.classList.toggle('has-error', hasError);
	}

	function validateStep(step, shouldFocus) {
		var fields = getStepFields(step);
		var firstInvalidField = null;
		var invalidCount = 0;

		fields.forEach(function (field) {
			var isValid = validateField(field);

			if (!isValid) {
				invalidCount += 1;
			}

			if (!isValid && !firstInvalidField) {
				firstInvalidField = field;
			}
		});

		setStepAlert(step, invalidCount);

		if (firstInvalidField) {
			if (shouldFocus) {
				firstInvalidField.focus();
			}

			return false;
		}

		return true;
	}

	function refreshValidatedStep(step) {
		var hasVisibleStepAlert = Boolean(
			step.querySelector('.cas-formflow-step-alert:not([hidden])')
		);

		if (hasVisibleStepAlert) {
			return validateStep(step, false);
		}

		return true;
	}

	function setActiveStep(steps, progressItems, activeIndex, shouldFocus) {
		steps.forEach(function (step, index) {
			var isActive = index === activeIndex;

			step.classList.toggle('is-active', isActive);
			step.hidden = !isActive;
			step.setAttribute('aria-hidden', isActive ? 'false' : 'true');
		});

		progressItems.forEach(function (item, index) {
			var isActive = index === activeIndex;

			item.classList.toggle('is-active', isActive);
			item.classList.toggle('is-complete', index < activeIndex);

			if (isActive) {
				item.setAttribute('aria-current', 'step');
			} else {
				item.removeAttribute('aria-current');
			}
		});

		if (shouldFocus) {
			var focusableField = getFocusableField(steps[activeIndex]);

			if (focusableField) {
				focusableField.focus();
			}
		}
	}

	function initFormflow(formflow) {
		var form = formflow.querySelector('.cas-formflow-form');
		var steps = Array.prototype.slice.call(
			formflow.querySelectorAll('.cas-formflow-step')
		);
		var progressItems = Array.prototype.slice.call(
			formflow.querySelectorAll('.cas-formflow-progress-item')
		);
		var activeIndex = steps.findIndex(function (step) {
			return step.classList.contains('is-active') && !step.hidden;
		});

		if (!steps.length) {
			return;
		}

		if (activeIndex < 0) {
			activeIndex = 0;
		}

		setActiveStep(steps, progressItems, activeIndex, false);

		formflow.addEventListener('blur', function (event) {
			var target = event.target;

			if (target instanceof HTMLElement && target.matches(FIELD_SELECTOR)) {
				validateField(target);
				updateProgressErrorState(
					progressItems,
					activeIndex,
					!refreshValidatedStep(steps[activeIndex])
				);
			}
		}, true);

		formflow.addEventListener('input', function (event) {
			var target = event.target;

			if (
				target instanceof HTMLElement &&
				target.matches(FIELD_SELECTOR) &&
				target.getAttribute('data-cas-validated') === 'true'
			) {
				setFieldError(target, getFieldErrorMessage(target));
				updateProgressErrorState(
					progressItems,
					activeIndex,
					!refreshValidatedStep(steps[activeIndex])
				);
			}
		});

		formflow.addEventListener('change', function (event) {
			var target = event.target;

			if (target instanceof HTMLElement && target.matches(FIELD_SELECTOR)) {
				validateField(target);
				updateProgressErrorState(
					progressItems,
					activeIndex,
					!refreshValidatedStep(steps[activeIndex])
				);
			}
		});

		formflow.addEventListener('click', function (event) {
			var target = event.target;

			if (!(target instanceof HTMLElement)) {
				return;
			}

			var nextButton = target.closest('.cas-formflow-button-next');
			var backButton = target.closest('.cas-formflow-button-back');
			var nextIndex = activeIndex;

			if (nextButton) {
				if (!validateStep(steps[activeIndex], true)) {
					updateProgressErrorState(progressItems, activeIndex, true);
					return;
				}

				updateProgressErrorState(progressItems, activeIndex, false);
				nextIndex = Math.min(activeIndex + 1, steps.length - 1);
			} else if (backButton) {
				nextIndex = Math.max(activeIndex - 1, 0);
			} else {
				return;
			}

			event.preventDefault();

			if (nextIndex === activeIndex) {
				return;
			}

			activeIndex = nextIndex;
			setActiveStep(steps, progressItems, activeIndex, true);
		});

		if (form) {
			form.addEventListener('submit', function (event) {
				var firstInvalidStepIndex = -1;

				steps.forEach(function (step, index) {
					var isValid = validateStep(step, false);

					updateProgressErrorState(progressItems, index, !isValid);

					if (firstInvalidStepIndex < 0 && !isValid) {
						firstInvalidStepIndex = index;
					}
				});

				if (firstInvalidStepIndex >= 0) {
					event.preventDefault();
					activeIndex = firstInvalidStepIndex;
					setActiveStep(steps, progressItems, activeIndex, true);
				}
			});
		}
	}

	function initFormflows() {
		var formflows = document.querySelectorAll('[data-cas-formflow]');

		if (!formflows.length) {
			return;
		}

		formflows.forEach(initFormflow);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initFormflows);
	} else {
		initFormflows();
	}
})();
