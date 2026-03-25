/**
 * Block Editor Meta Fields Sidebar
 *
 * Registers a PluginDocumentSettingPanel for each field section defined by
 * the PHP class. Field definitions are passed via the `starterPluginMetaFields`
 * global object that is localised by Starter_Plugin_Post_Type::enqueue_block_editor_assets().
 *
 * When the classic editor is active for a post type this script is never
 * enqueued, so legacy meta boxes remain in use instead.
 *
 * @package Starter_Plugin
 * @since   1.0.0
 */
( function () {
	var fieldData = window.starterPluginMetaFields;

	if ( ! fieldData || ! fieldData.fields || ! fieldData.fields.length ) {
		return;
	}

	var el                         = wp.element.createElement;
	var Fragment                   = wp.element.Fragment;
	var registerPlugin             = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editor.PluginDocumentSettingPanel;
	var TextControl                = wp.components.TextControl;
	var useSelect                  = wp.data.useSelect;
	var useDispatch                = wp.data.useDispatch;

	if ( ! PluginDocumentSettingPanel ) {
		return;
	}

	// Group fields by their declared section key.
	var fieldsBySection = {};
	fieldData.fields.forEach( function ( field ) {
		var section = field.section || 'default';
		if ( ! fieldsBySection[ section ] ) {
			fieldsBySection[ section ] = [];
		}
		fieldsBySection[ section ].push( field );
	} );

	var sectionKeys = Object.keys( fieldsBySection );

	/**
	 * Renders one sidebar panel for each field section.
	 *
	 * Meta values are read from and written to the block editor's post entity
	 * through the `core/editor` data store, so they are saved automatically
	 * when the editor saves the post.
	 */
	function MetaFieldsPanels() {
		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		} );

		var { editPost } = useDispatch( 'core/editor' );

		function handleChange( metaKey, value ) {
			var update        = {};
			update[ metaKey ] = value;
			editPost( { meta: update } );
		}

		return el(
			Fragment,
			null,
			sectionKeys.map( function ( sectionKey ) {
				var sectionLabel =
					fieldData.sections && fieldData.sections[ sectionKey ]
						? fieldData.sections[ sectionKey ]
						: sectionKey;

				return el(
					PluginDocumentSettingPanel,
					{
						key:       sectionKey,
						name:      fieldData.postType + '-' + sectionKey,
						title:     sectionLabel,
						className: 'starter-plugin-meta-panel',
					},
					fieldsBySection[ sectionKey ].map( function ( field ) {
						var metaKey = '_' + field.key;
						var value =
							meta[ metaKey ] !== undefined
								? meta[ metaKey ]
								: field.default || '';

						return el( TextControl, {
							key:      field.key,
							label:    field.name,
							help:     field.description,
							value:    value,
							type:     field.type === 'url' ? 'url' : 'text',
							onChange: function ( newValue ) {
								handleChange( metaKey, newValue );
							},
						} );
					} )
				);
			} )
		);
	}

	registerPlugin( 'starter-plugin-meta-fields-' + fieldData.postType, {
		render: MetaFieldsPanels,
	} );
} )();