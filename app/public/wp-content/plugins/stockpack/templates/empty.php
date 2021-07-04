<script type="text/html" id="tmpl-stockpack-empty">
	<# var messageClass = data.message ? 'has-message' : 'no-message'; #>
	<# var errorClass = data.error ? 'has-error' : 'no-error'; #>
	<div class="no-data {{ messageClass }} {{errorClass}}">
		<# if ( data.message ) { #>
		<h2 class="stockpack-message">{{ data.message }}</h2>
		<# } #>
		<# if ( data.error ) { #>
		<pre class="stockpack-error">{{{ data.error }}}</pre>
		<# } #>
		<# if ( data.link ) { #>
		<a  target="_blank" href="{{ data.link.url }}" class="stockpack-link">{{ data.link.text }}</a>
		<# } #>
		<# if ( data.retry ) { #>
		<button type="button" class="button media-button button-primary button-large retry">{{data.retry}}</button>
		<# } #>
	</div>
</script>