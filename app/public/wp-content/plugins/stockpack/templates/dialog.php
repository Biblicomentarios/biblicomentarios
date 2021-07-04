<script type="text/html" id="tmpl-stockpack-dialog">
<div class="message">
	<# if ( data.message ) { #>
	<p>{{ data.message }}</p>
	<# } #>
	<# if ( data.link ) { #>
	<a target="_blank" href="{{ data.link }}">{{ data.link }}</a>
	<# } #>
	<# if ( data.state ) { #>
	<p class="stockpack-dialog-state">{{ data.state }}</p>
	<# } #>
	<# if ( data.externalUrl ) { #>
	<p class="stockpack-dialog-external">{{ data.external }}</p>
	<a target="_blank" href="{{ data.externalUrl }}">{{ data.directLicenseUrl }}</a>
	<# } #>
	<# if ( data.iframe ) { #>
	<div class="iframe-status"> {{ data.iframe.status}}</div>
	<iframe width="100%" height="450px" src="{{ data.iframe.src }}" id="{{ data.iframe.id }}"/>
	<# } #>
	<# if ( data.error ) { #>
	<p class="stockpack-error">{{{ data.error }}}</p>
	<# } #>
</div>
</script>
