jQuery(document).ready(function($) {

	tinymce.create('tinymce.plugins.tooltip', {
		init : function(ed, url) {

			// Action called on button click
			ed.addCommand('tooltip_action', function() {
				var t  = tinyMCE.activeEditor.selection.getContent({format : 'text'});
				var content = '[tooltip texte=""]'+t+'[/tooltip]';

				tinymce.execCommand('mceInsertContent', false, content);
			});

			// Add custom button and link to action
			ed.addButton('tooltip', {
				title : 'Créer un tooltip',
				cmd : 'tooltip_action',
				image: url + '/tool-tip.png'
			});
		},
	});

	// Register our TinyMCE plugin
	// first parameter is the button ID1
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('tooltip', tinymce.plugins.tooltip);

});