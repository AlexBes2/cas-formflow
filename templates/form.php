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

<div class="cas-formflow container my-5" data-cas-formflow>
	<div class="row justify-content-center">
		<div class="col-12 col-md-10 col-lg-8 col-xl-6">
			<form class="cas-formflow-form" method="post" action="">
				<div class="cas-formflow-steps">

					<div class="cas-formflow-step is-active" data-step="1">
						<div class="cas-formflow-step-inner card shadow-sm border-0">
							<div class="card-body p-4 p-md-5">
								<h2 class="cas-formflow-step-title h4 mb-3">Step 1</h2>
								<p class="cas-formflow-step-description text-muted mb-4">Enter your first name.</p>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label" for="cas-first-name">First Name</label>
									<input
										class="cas-formflow-input form-control"
										type="text"
										id="cas-first-name"
										name="first_name"
										placeholder="Enter your first name"
									>
								</div>

								<div class="cas-formflow-actions d-grid d-sm-flex justify-content-sm-end gap-2">
									<button type="button" class="cas-formflow-button cas-formflow-button-next btn btn-primary">
										Next
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="cas-formflow-step" data-step="2" hidden>
						<div class="cas-formflow-step-inner card shadow-sm border-0">
							<div class="card-body p-4 p-md-5">
								<h2 class="cas-formflow-step-title h4 mb-3">Step 2</h2>
								<p class="cas-formflow-step-description text-muted mb-4">Enter your contact details.</p>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label" for="cas-phone">Phone</label>
									<input
										class="cas-formflow-input form-control"
										type="text"
										id="cas-phone"
										name="phone"
										placeholder="Enter your phone"
									>
								</div>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label" for="cas-email">Email</label>
									<input
										class="cas-formflow-input form-control"
										type="email"
										id="cas-email"
										name="email"
										placeholder="Enter your email"
									>
								</div>

								<div class="cas-formflow-actions d-grid d-sm-flex justify-content-sm-between gap-2">
									<button type="button" class="cas-formflow-button cas-formflow-button-back btn btn-outline-secondary order-2 order-sm-1">
										Back
									</button>

									<button type="button" class="cas-formflow-button cas-formflow-button-next btn btn-primary order-1 order-sm-2">
										Next
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="cas-formflow-step" data-step="3" hidden>
						<div class="cas-formflow-step-inner card shadow-sm border-0">
							<div class="card-body p-4 p-md-5">
								<h2 class="cas-formflow-step-title h4 mb-3">Step 3</h2>
								<p class="cas-formflow-step-description text-muted mb-4">Enter your message and submit the form.</p>

								<div class="cas-formflow-field mb-3">
									<label class="cas-formflow-label form-label" for="cas-description">Message</label>
									<textarea
										class="cas-formflow-textarea form-control"
										id="cas-description"
										name="description"
										rows="5"
										placeholder="Enter your message"
									></textarea>
								</div>

								<div class="cas-formflow-actions d-grid d-sm-flex justify-content-sm-between gap-2">
									<button type="button" class="cas-formflow-button cas-formflow-button-back btn btn-outline-secondary order-2 order-sm-1">
										Back
									</button>

									<button type="submit" class="cas-formflow-button cas-formflow-button-submit btn btn-success order-1 order-sm-2">
										Submit
									</button>
								</div>
							</div>
						</div>
					</div>

				</div>
			</form>
		</div>
	</div>
</div>
