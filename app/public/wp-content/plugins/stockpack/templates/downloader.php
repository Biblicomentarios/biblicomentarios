<script type="text/html" id="tmpl-stockpack-downloader">
<div class="downloader">
    <# if ( data.filename_change ) { #>
    <div class="filename-change">
        <input type="text" class="regular-text" value="{{data.desired_filename}}" placeholder="{{data.filename_placeholder}}">
    </div>
    <# } #>
    <button type="button" class="button media-button button-primary button-large download-stockpack-image">
        {{data.download}}
    </button>
    <# if ( data.model.extra ) { #>
    <# if ( !data.model.extra.licensed ) { #>
    <button type="button" class="button media-button button-secondary button-large license-stockpack-image">
        {{data.license}}
    </button>
    <# } else { #>
    <span class="stockpack-already-licensed">{{data.alreadyLicensed}}</span>
    <# } #>
    <# } #>
    <# if ( data.state ) { #>
    <span class="state">{{data.state}}</span>
    <# } #>
    <# if ( data.progress ) { #>
    <span class="progress">{{data.progress}}</span>
    <# } #>
    <# if ( data.warning ) { #>
    <div class="warning">
        <div class="notice notice-warning">
            {{data.warning}}
        </div>
    </div>
    <# } #>
</div>
</script>
