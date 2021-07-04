<script type="text/html" id="tmpl-stockpack-attachment-details">
<h2>
	Image Details <span class="settings-save-status">
				<span class="spinner"></span>
				<span class="saved">Saved.</span>
			</span>
</h2>
<div class="attachment-info">
	<div class="thumbnail thumbnail-{{ data.type }}">
		<# if ( data.uploading ) { #>
		<div class="media-progress-bar">
			<div></div>
		</div>
		<# } else if ( data.sizes ) { #>
		<img src="{{ data.sizes.full.url }}" draggable="false" alt=""/>
		<# } #>
	</div>
	<div class="details">
		<div class="description">{{ data.description }}</div>
		<div class="caption">{{{ data.caption }}}</div>
	</div>
</div>
</script>