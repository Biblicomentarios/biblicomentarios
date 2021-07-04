import './editor.css';

const {__} = wp.i18n;
const {registerPlugin} = wp.plugins;
const {PluginSidebar} = wp.editPost;
const {SelectControl} = wp.components;
const {withSelect, withDispatch} = wp.data;
const {Component} = wp.element;

class Daext_Autolinks_Manager extends Component {

  constructor(){

    super(...arguments);

    /**
     * If the '_daextam_enable_autolinks' meta of this post is not defined get its value from the plugin options from a
     * custom endpoint of the WordPress Rest API.
     */
    if(wp.data.select('core/editor').getEditedPostAttribute('meta')['_daextam_enable_autolinks'].length === 0){

      wp.apiFetch( { path: '/daext-autolinks-manager/v1/options', method: 'GET' } ).then(
          ( data ) => {

            wp.data.dispatch( 'core/editor' ).editPost(
                { meta: { _daextam_enable_autolinks: data.daextam_advanced_enable_autolinks } }
            );

          },
          ( err ) => {

            return err;

          }
      );

    }

  }

  render() {

    const MetaBlockField = function(props) {
      return (
          <SelectControl
              label={__('Enable Autolinks', 'daextam')}
              value={props.metaFieldValue}
              options={[
                {value: '0', label: __('No', 'daextam')},
                {value: '1', label: __('Yes', 'daextam')},
              ]}
              onChange={function(content) {
                props.setMetaFieldValue(content);
              }}
          >
          </SelectControl>
      );
    };

    const MetaBlockFieldWithData = withSelect(function(select) {
      return {
        metaFieldValue: select('core/editor').getEditedPostAttribute('meta')
            ['_daextam_enable_autolinks'],
      };
    })(MetaBlockField);

    const MetaBlockFieldWithDataAndActions = withDispatch(
        function(dispatch) {
          return {
            setMetaFieldValue: function(value) {
              dispatch('core/editor').editPost(
                  {meta: {_daextam_enable_autolinks: value}},
              );
            },
          };
        },
    )(MetaBlockFieldWithData);

    return (
        <PluginSidebar
            name='daext-autolinks-manager-sidebar'
            icon='admin-links'
            title={__('Autolinks Manager', 'daextam')}
        >
          <div
              className='daext-autolinks-manager-sidebar-content'
          >
            <MetaBlockFieldWithDataAndActions></MetaBlockFieldWithDataAndActions>
          </div>
        </PluginSidebar>
    );

  }

}

registerPlugin('daextam-autolinks-manager', {
  render: Daext_Autolinks_Manager,
});