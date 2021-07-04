<?php
/**
 * Step Two Template
 *
 * The template wrapper for the step one set up page.
 *
 * @package ShareThisShareButtons
 */

?>
<a href="?page=sharethis-general" class="st-rc-back" type="button">BACK</a>
<div id="sharethis-step-two-wrap">
	<a href="#" class="st-rc-link set-config" type="button">REGISTER</a>

	<div class="sharethis-setup-steps">
		<?php
		foreach ( $setup_steps as $num => $step ) :
			$step_class = 2 === $num ? 'current-step' : '';
			$step_class = 1 === $num ? 'finished-step' : $step_class;
			$num = 1 === $num ? '<img src="' . esc_url( "{$this->plugin->dir_url}/assets/finished-step.png" ) . '">' : $num;
			?>

			<span class="step-num <?php echo esc_attr( $step_class ); ?>"><?php echo wp_kses_post( $num ); ?></span>

			<div class="step-description"><?php echo esc_html( $step ); ?></div>

			<span class="step-spacer"></span>
		<?php endforeach; ?>
	</div>

	<h4 class="selected-button" id="<?php echo esc_attr( $button ); ?>">
		<?php echo esc_html( $button . __( ' buttons, excellent! Now its time to design.', 'sharethis-share-buttons' ) ); ?>
	</h4>

	<div class="sharethis-selected-networks">
		<div id="<?php echo esc_attr( $button ); ?>-8" class="sharethis-<?php echo esc_attr( strtolower( $button ) ); ?>-share-buttons"></div>
	</div>

	<?php if ( 'Inline' === $button ) : ?>
		<p class="st-preview-message">
			⇧ <?php echo esc_html__( 'Preview: click and drag to reorder' ); ?> ⇧
		</p>
	<?php endif; ?>

	<div class="button-configuration-wrap">
		<h2><?php echo esc_html__( 'Social networks', 'sharethis-share-buttons' ); ?></h2>

		<span>click a network to add or remove it from your preview. We've already included the most popular networks.</span>

		<div class="share-buttons">
			<?php
			foreach ( $networks as $network_name => $network_info ) :
				$viewbox = isset( $network_info['viewbox'] ) ? '0 0 60 60' : '0 0 40 40';
				$viewbox = isset( $network_info['viewbox-total'] ) ? esc_attr( $network_info['viewbox-total'] ) : $viewbox;
				?>
				<div class="share-button" data-color="<?php echo esc_attr( $network_info['color'] ); ?>" data-selected="<?php echo esc_attr( $network_info['selected'] ); ?>" data-network="<?php echo esc_attr( $network_name ); ?>" title="<?php echo esc_attr( $network_name ); ?>" style="background: rgb(<?php echo esc_attr( $network_info['color-rgba'] ); ?>);">
					<?php if ( isset( $network_info['full-svg'] ) ) : ?>
						<?php echo $network_info['full-svg']; ?>
					<?php else : ?>
						<svg fill="#fff" preserveAspectRatio="xMidYMid meet" height="2em" width="2em" viewBox="<?php echo esc_attr( $viewbox ); ?>">
							<?php echo ! empty( $network_info['shape'] ) ? $network_info['shape'] : ''; ?>
							<g>
								<?php if ( is_array( $network_info['path'] ) ) : ?>
									<?php foreach ( $network_info['path'] as $path_code ) : ?>
										<path d="<?php echo esc_attr( $path_code ); ?>"></path>
									<?php endforeach; ?>
								<?php else : ?>
									<path d="<?php echo esc_attr( $network_info['path'] ); ?>"></path>
								<?php endif; ?>
							</g>
						</svg>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<span>
			<div class="notes">
				<span style="background: rgb(255, 189, 0); border-radius: 20px; font-size: 20px; margin: 0 .5rem; padding: 6px 0 0 5px;">
					<svg fill="#fff" preserveAspectRatio="xMidYMid meet" height="1em" width="1em" viewBox="0 0 40 40">
						<g>
							<path d="M29.577,23.563 C27.233,23.563 25.935,22.138 25.935,22.138 L27.22,20.283 C27.22,20.283 28.349,21.315 29.605,21.315 C30.108,21.315 30.652,21.12 30.652,20.52 C30.652,19.334 26.158,19.376 26.158,16.306 C26.158,14.464 27.707,13.25 29.688,13.25 C31.839,13.25 32.898,14.38 32.898,14.38 L31.866,16.376 C31.866,16.376 30.861,15.497 29.661,15.497 C29.159,15.497 28.6,15.72 28.6,16.278 C28.6,17.534 33.094,17.311 33.094,20.464 C33.094,22.125 31.824,23.563 29.577,23.563 L29.577,23.563 Z M23.027,23.394 L22.721,18.901 C22.665,18.147 22.721,17.227 22.721,17.227 L22.692,17.227 C22.692,17.227 22.356,18.273 22.134,18.901 L21.088,21.79 L18.994,21.79 L17.947,18.901 C17.724,18.273 17.389,17.227 17.389,17.227 L17.361,17.227 C17.361,17.227 17.417,18.147 17.361,18.901 L17.055,23.394 L14.598,23.394 L15.422,13.417 L18.073,13.417 L19.524,17.631 C19.748,18.273 20.026,19.278 20.026,19.278 L20.055,19.278 C20.055,19.278 20.334,18.273 20.557,17.631 L22.008,13.417 L24.66,13.417 L25.469,23.394 L23.027,23.394 Z M10.548,23.563 C8.204,23.563 6.906,22.138 6.906,22.138 L8.19,20.283 C8.19,20.283 9.32,21.315 10.576,21.315 C11.078,21.315 11.623,21.12 11.623,20.52 C11.623,19.334 7.129,19.376 7.129,16.306 C7.129,14.464 8.678,13.25 10.66,13.25 C12.808,13.25 13.869,14.38 13.869,14.38 L12.836,16.376 C12.836,16.376 11.832,15.497 10.632,15.497 C10.129,15.497 9.571,15.72 9.571,16.278 C9.571,17.534 14.064,17.311 14.064,20.464 C14.064,22.125 12.795,23.563 10.548,23.563 L10.548,23.563 Z M32.814,6 L7.185,6 C5.437,6 4,7.438 4,9.213 L4,28.99 C4,30.756 5.426,32.203 7.185,32.203 L10.61,32.203 L12.445,34.295 C13.086,34.952 14.117,34.949 14.755,34.295 L16.59,32.203 L32.814,32.203 C34.562,32.203 36,30.764 36,28.99 L36,9.213 C36,7.446 34.574,6 32.814,6 L32.814,6 Z"></path>
						</g>
					</svg>
				</span>

				<?php echo esc_html__( 'The sms button only appears on mobile devices. It is included in your desktop preview for reference only.', 'sharethis-share-buttons' ); ?>
			</div>
		</span>

		<hr>

		<?php if ( 'Inline' === $button ) : ?>
			<div class="button-alignment">
				<h3>Alignment</h3>

				<div class="alignment-button" data-alignment="left" data-selected="false">
					<div class="top">
						<div class="box"></div>
						<div class="box"></div>
						<div class="box"></div>
					</div>
					<div class="bottom">Left</div>
				</div>

				<div class="alignment-button" data-alignment="center" data-selected="true">
					<div class="top">
						<div class="box"></div>
						<div class="box"></div>
						<div class="box"></div>
					</div>
					<div class="bottom">Center</div>
				</div>

				<div class="alignment-button" data-alignment="right" data-selected="false">
					<div class="top">
						<div class="box"></div>
						<div class="box"></div>
						<div class="box"></div>
					</div><div class="bottom">Right</div>
				</div>

				<div class="alignment-button" data-alignment="justified" data-selected="false">
					<div class="top">
						<div class="box"></div>
						<div class="box"></div>
						<div class="box"></div>
					</div>
					<div class="bottom">Justified</div>
				</div>
			</div>

			<hr>

		<?php endif; ?>

		<div class="row">

			<?php if ( 'Inline' === $button ) : ?>
				<div class="st-radio-config button-config button-size">
					<h3>Size</h3>

					<div class="item">
						<input type="radio" class="with-gap" value="on" checked="checked">

						<label>Small</label>
					</div>
					<div class="item">
						<input type="radio" class="with-gap" value="on">
						<label>Medium</label>
					</div>
					<div class="item">
						<input type="radio" class="with-gap" value="on">
						<label>Large</label>
					</div>
			<?php else : ?>
				<div class="button-config">
					<h3>Alignment</h3>

					<div class="item">
						<label>
							<span>Left</span>

							<div class="switch sticky-alignment">
								<label>
									<input type="checkbox" value="on">

									<span class="lever"></span>
								</label>
							</div>

							<span>Right</span>
						</label>
					</div>
					<div class="item">
						<span class="lbl">Vertical Alignment</span>

						<input class="vertical-alignment" type="text" value="160">
					</div>
					<div class="item">
						<span class="lbl">Mobile Breakpoint</span>

						<input class="mobile-breakpoint" type="text" value="1024">
					</div>
			<?php endif; ?>
			</div>
			<div class="st-radio-config button-config button-labels">
				<h3>Labels</h3>

				<div class="item">
					<input type="radio" class="with-gap" value="on" checked="checked">

					<label id="cta">Call to Action</label>
				</div>
				<div class="item">
					<input type="radio" class="with-gap" value="on">

					<label id="counts">Share Counts</label>
				</div>
				<div class="item">
					<input type="radio" class="with-gap" value="on">

					<label id="none">None</label>
				</div>
			</div>

			<div class="button-config">
				<h3>Counts</h3>

				<div class="item">
					<span class="lbl show-total-count">Show total count</span>

					<div class="switch">
						<label>
							<input type="checkbox" value="on" checked="checked">

							<span class="lever"></span>
						</label>
					</div>
				</div>
				<div class="item tooltip">
					<span class="lbl">
						Minimum Count
						<span class="tooltip-icon tooltipped" data-delay="50" data-position="right" data-tooltip="This is the minimum number of shares a page needs to have before we'll show your share counts." data-tooltip-id="233a37b7-7c96-eb8b-128e-80c62a922f41">
							<svg fill="#fff" preserveAspectRatio="xMidYMid meet" height="1em" width="1em" viewBox="0 0 40 40">
								<g>
								<path d="m23.2 28v5.4q0 0.4-0.3 0.6t-0.6 0.3h-5.3q-0.4 0-0.7-0.3t-0.2-0.6v-5.4q0-0.3 0.2-0.6t0.7-0.3h5.3q0.4 0 0.6 0.3t0.3 0.6z m7.1-13.4q0 1.2-0.4 2.3t-0.8 1.7-1.2 1.3-1.3 1-1.3 0.8q-0.9 0.5-1.6 1.4t-0.6 1.5q0 0.4-0.2 0.8t-0.7 0.3h-5.3q-0.4 0-0.6-0.4t-0.2-0.8v-1q0-1.9 1.4-3.5t3.2-2.5q1.3-0.6 1.9-1.2t0.5-1.7q0-0.9-1-1.7t-2.4-0.7q-1.4 0-2.4 0.7-0.8 0.5-2.4 2.5-0.3 0.4-0.7 0.4-0.2 0-0.5-0.2l-3.7-2.8q-0.3-0.2-0.3-0.5t0.1-0.6q3.5-6 10.3-6 1.8 0 3.6 0.7t3.3 1.9 2.4 2.8 0.9 3.5z"></path>
								</g>
							</svg>
						</span>
						<div class="material-tooltip"><span>This is the minimum number of shares a page needs to have before we'll show your share counts.</span><div class="backdrop" style="top: -7px; left: 0px; width: 14px; height: 14px; border-radius: 0px 14px 14px 0px; transform-origin: 5% 50% 0px; margin-top: 31px; margin-left: 0px; display: none; opacity: 0; transform: scaleX(1) scaleY(1);"></div></div>
					</span>

					<input class="minimum-count" type="text" value="10">
				</div>
			</div>

			<hr>

					<div class="button-config">
						<h3 class="center"><?php echo esc_html__( 'Corners', 'sharethis-share-buttons' ); ?></h3>

						<span><?php echo esc_html__( 'Square', 'sharethis-share-buttons' ); ?></span>
						<span class="range-field">
					<input type="range" min="0" max="16" value="4" id="radius-selector" style="width: 200px; margin: 5px;">
					<span class="thumb">
						<span class="value"></span>
					</span>
				</span>
						<span><?php echo esc_html__( 'Rounded', 'sharethis-share-buttons' ); ?></span>
					</div>
					<div class="button-config">
						<h3><?php echo esc_html__( 'Extras', 'sharethis-share-buttons' ); ?></h3>

						<?php if ( 'Inline' === $button ) : ?>
							<div class="item">
								<span class="lbl extra-spacing"><?php echo esc_html__( 'Add Spacing', 'sharethis-share-buttons' ); ?></span>

								<div class="switch">
									<label>
										<input type="checkbox" value="on" checked="checked">

										<span class="lever"></span>
									</label>
								</div>
							</div>
						<?php else : ?>
							<div class="item">
								<span class="lbl show-on-mobile"><?php echo esc_html__( 'Show on mobile', 'sharethis-share-buttons' ); ?></span>

								<div class="switch">
									<label>
										<input type="checkbox" value="on" checked="checked">

										<span class="lever"></span>
									</label>
								</div>
							</div>
							<div class="item">
								<span class="lbl show-on-desktop"><?php echo esc_html__( 'Hide on desktop', 'sharethis-share-buttons' ); ?></span>

								<div class="switch">
									<label>
										<input type="checkbox" value="on">

										<span class="lever"></span>
									</label>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<div class="button-config">
						<h3 class="center"><?php echo esc_html__( 'Languages', 'sharethis-share-buttons' ); ?></h3>
						<span class="select-field">
					<select id="st-language">
						<?php foreach ( $languages as $language_name => $code ) : ?>
							<option class="language-option" value="<?php echo esc_attr( $code ); ?>">
								<?php echo esc_html( $language_name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</span>
					</div>
		</div>
	</div>

	<h4><?php echo esc_html__( 'Ooooo that\'s hot. Click register & configure to complete installation!', 'sharethis-share-buttons' ); ?></h4>

	<a href="#" class="st-rc-link set-congif">REGISTER</a>
</div>
