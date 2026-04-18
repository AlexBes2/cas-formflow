<?php
/**
 * Form template.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="cas-formflow container my-5 py-4 px-3 px-sm-4" data-cas-formflow>
	<div class="row justify-content-center">
		<div class="col-12">
			<form class="cas-formflow-form" method="post" action="" novalidate>
				<ol class="cas-formflow-progress mb-3" aria-label="Form progress">
					<li class="cas-formflow-progress-item is-active" data-progress-step="1" aria-current="step">
						<span class="cas-formflow-progress-number">1</span>
					</li>
					<li class="cas-formflow-progress-item" data-progress-step="2">
						<span class="cas-formflow-progress-number">2</span>
					</li>
					<li class="cas-formflow-progress-item" data-progress-step="3">
						<span class="cas-formflow-progress-number">3</span>
					</li>
				</ol>

				<div class="cas-formflow-steps">

					<section class="cas-formflow-step is-active" data-step="1" aria-labelledby="cas-formflow-step-1-title">
						<div class="cas-formflow-step-inner card border">
							<div class="card-body p-4">
								<h2 class="cas-formflow-step-title h6 fw-bold mb-1" id="cas-formflow-step-1-title">Personal info</h2>
								<p class="cas-formflow-step-description text-secondary small mb-3">Step 1 of 3</p>

								<div class="row g-3">
									<div class="cas-formflow-field col-12 col-sm-6">
										<label class="cas-formflow-label form-label small mb-1" for="cas-first-name">First name *</label>
										<input
											class="cas-formflow-input form-control form-control-sm"
											type="text"
											id="cas-first-name"
											name="first_name"
											placeholder="John"
											autocomplete="given-name"
											required
										>
									</div>

									<div class="cas-formflow-field col-12 col-sm-6">
										<label class="cas-formflow-label form-label small mb-1" for="cas-last-name">Last name *</label>
										<input
											class="cas-formflow-input form-control form-control-sm"
											type="text"
											id="cas-last-name"
											name="last_name"
											placeholder="Doe"
											autocomplete="family-name"
											required
										>
									</div>

									<div class="cas-formflow-field col-12">
										<label class="cas-formflow-label form-label small mb-1" for="cas-email">Email *</label>
										<input
											class="cas-formflow-input form-control form-control-sm"
											type="email"
											id="cas-email"
											name="email"
											placeholder="john@example.com"
											autocomplete="email"
											required
										>
									</div>

									<div class="cas-formflow-field col-12">
										<label class="cas-formflow-label form-label small mb-1" for="cas-phone">Phone *</label>
										<input
											class="cas-formflow-input form-control form-control-sm"
											type="tel"
											id="cas-phone"
											name="phone"
											placeholder="+380 (XX) XXX-XX-XX"
											autocomplete="tel"
											required
										>
									</div>

									<div class="cas-formflow-field col-12">
										<label class="cas-formflow-label form-label small mb-1" for="cas-date-of-birth">Date of birth</label>
										<input
											class="cas-formflow-input form-control form-control-sm"
											type="date"
											id="cas-date-of-birth"
											name="date_of_birth"
										>
									</div>
								</div>

								<div class="cas-formflow-actions d-flex justify-content-end mt-3">
									<button type="button" class="cas-formflow-button cas-formflow-button-next btn btn-primary btn-sm px-4">
										Next &rarr;
									</button>
								</div>
							</div>
						</div>
					</section>

					<section class="cas-formflow-step" data-step="2" aria-labelledby="cas-formflow-step-2-title" hidden>
						<div class="cas-formflow-step-inner card border">
							<div class="card-body p-4">
								<h2 class="cas-formflow-step-title h6 fw-bold mb-1" id="cas-formflow-step-2-title">Address</h2>
								<p class="cas-formflow-step-description text-secondary small mb-3">Step 2 of 3</p>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label small mb-1" for="cas-country">Country *</label>
									<select
										class="cas-formflow-input cas-formflow-select form-select form-select-sm"
										id="cas-country"
										name="country"
										required
									>
										<option value="">Select country...</option>
										<option value="Ukraine">Ukraine</option>
										<option value="United States">United States</option>
										<option value="United Kingdom">United Kingdom</option>
										<option value="Germany">Germany</option>
										<option value="Poland">Poland</option>
									</select>
								</div>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label small mb-1" for="cas-city">City *</label>
									<input
										class="cas-formflow-input form-control form-control-sm"
										type="text"
										id="cas-city"
										name="city"
										placeholder="Odesa"
										autocomplete="address-level2"
										required
									>
								</div>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label small mb-1" for="cas-street-address">Street address</label>
									<input
										class="cas-formflow-input form-control form-control-sm"
										type="text"
										id="cas-street-address"
										name="street_address"
										placeholder="123 Main St, Apt 4"
										autocomplete="street-address"
									>
								</div>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label small mb-1" for="cas-postal-code">ZIP / Postal code</label>
									<input
										class="cas-formflow-input form-control form-control-sm"
										type="text"
										id="cas-postal-code"
										name="postal_code"
										placeholder="65000"
										autocomplete="postal-code"
									>
								</div>

								<div class="cas-formflow-actions d-flex justify-content-between gap-2 mt-3">
									<button type="button" class="cas-formflow-button cas-formflow-button-back btn btn-outline-secondary btn-sm px-4">
										&larr; Back
									</button>

									<button type="button" class="cas-formflow-button cas-formflow-button-next btn btn-primary btn-sm px-4">
										Next &rarr;
									</button>
								</div>
							</div>
						</div>
					</section>

					<section class="cas-formflow-step" data-step="3" aria-labelledby="cas-formflow-step-3-title" hidden>
						<div class="cas-formflow-step-inner card border">
							<div class="card-body p-4">
								<h2 class="cas-formflow-step-title h6 fw-bold mb-1" id="cas-formflow-step-3-title">Confirmation</h2>
								<p class="cas-formflow-step-description text-secondary small mb-3">Step 3 of 3</p>

								<div class="cas-formflow-checkbox form-check mb-2">
									<input
										class="cas-formflow-checkbox-input form-check-input"
										type="checkbox"
										id="cas-terms"
										name="terms"
										value="1"
										required
									>
									<label class="cas-formflow-checkbox-label form-check-label small" for="cas-terms">
										I agree to the <a href="#" class="cas-formflow-link link-secondary">Terms and Conditions</a> *
									</label>
								</div>

								<div class="cas-formflow-checkbox form-check mb-3">
									<input
										class="cas-formflow-checkbox-input form-check-input"
										type="checkbox"
										id="cas-newsletter"
										name="newsletter"
										value="1"
									>
									<label class="cas-formflow-checkbox-label form-check-label small" for="cas-newsletter">
										Subscribe to newsletter
									</label>
								</div>

								<div class="cas-formflow-actions d-flex justify-content-between gap-2 mt-3">
									<button type="button" class="cas-formflow-button cas-formflow-button-back btn btn-outline-secondary btn-sm px-4">
										&larr; Back
									</button>

									<button type="submit" class="cas-formflow-button cas-formflow-button-submit btn btn-success btn-sm px-4">
										Submit &check;
									</button>
								</div>
							</div>
						</div>
					</section>

				</div>
			</form>
		</div>
	</div>
</div>
