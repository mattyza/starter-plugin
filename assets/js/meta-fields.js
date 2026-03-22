var PluginDocumentSettingPanel =
    ( wp.editor  && wp.editor.PluginDocumentSettingPanel ) ||
    ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );

// ... (other code)

var { editPost } = useDispatch( 'core/editor' );

// ... (other code)