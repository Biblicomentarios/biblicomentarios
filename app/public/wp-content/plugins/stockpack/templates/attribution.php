<script type="text/html" id="tmpl-stockpack-attribution">
<div class="attribution notice notice-warning is-dismissible">
	<# if ( data.message ) { #>
	<span>{{ data.message }}</span>
	<# } #>
	<# if ( data.link ) { #>
	<a target="_blank" href="{{ data.link }}">{{ data.link_title }}</a>
	<# } #>
	<# if ( data.author_info ) { #>
	<span class="author-info">{{ data.author_info }}</span>
	<# } #>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text">Dismiss this notice.</span>
	</button>
</div>
</script>
