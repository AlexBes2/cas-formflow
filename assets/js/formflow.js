(function () {
	'use strict';

	function getFocusableField(step) {
		return step.querySelector('input, select, textarea, button');
	}

	function getFieldWrapper(field) {
		return field.closest('.cas-formflow-field, .cas-formflow-checkbox');
	}

	function getFieldErrorId(field) {
		return field.id ? field.id + '-error' : '';
	}

	function getFieldErrorMessage(field) {
		var value = field.value.trim();

		if (field.required) {
			if (field.type === 'checkbox' && !field.checked) {
				return 'This field is required.';
			}

			if (field.type !== 'checkbox' && !value) {
				return 'This field is required.';
			}
		}

		if (field.type === 'email' && value && !field.validity.valid) {
			return 'Enter a valid email address.';
		}

		return '';
	}

	function setFieldError(field, message) {
		var wrapper = getFieldWrapper(field);
		var errorId = getFieldErrorId(field);
		var error = wrapper && errorId ? wrapper.querySelector('#' + errorId) : null;

		field.classList.toggle('is-invalid', Boolean(message));
		field.setAttribute('aria-invalid', message ? 'true' : 'false');

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
			field.setAttribute('aria-describedby', errorId);
		} else {
			field.removeAttribute('aria-describedby');
		}
	}

	function validateStep(step, shouldFocus) {
		var fields = Array.prototype.slice.call(
			step.querySelectorAll('input, select, textarea')
		);
		var firstInvalidField = null;

		fields.forEach(function (field) {
			var message = getFieldErrorMessage(field);

			setFieldError(field, message);

			if (message && !firstInvalidField) {
				firstInvalidField = field;
			}
		});

		if (firstInvalidField) {
			if (shouldFocus) {
				firstInvalidField.focus();
			}

			return false;
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

		formflow.addEventListener('input', function (event) {
			var target = event.target;

			if (target instanceof HTMLElement && target.matches('input, select, textarea')) {
				setFieldError(target, getFieldErrorMessage(target));
			}
		});

		formflow.addEventListener('change', function (event) {
			var target = event.target;

			if (target instanceof HTMLElement && target.matches('input, select, textarea')) {
				setFieldError(target, getFieldErrorMessage(target));
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
					return;
				}

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
					if (firstInvalidStepIndex < 0 && !validateStep(step, false)) {
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
