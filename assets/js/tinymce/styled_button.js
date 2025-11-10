jQuery(document).ready(function($) {

	tinymce.create('tinymce.plugins.styled_button', {
		init : function(ed, url) {

			// Action called on button click
			ed.addCommand('styled_button_action', function() {
				var node  = tinyMCE.activeEditor.selection.getNode();

				// make sure it's an <a>
				if( node.nodeName != 'A' ) {
					return;
				}

				var href = node.href;
				var text = node.innerText;
				var target = node.target;

				var content = '[bouton href="'+href+'" target="'+target+'" color="red"]'+text+'[/bouton]';

				node.remove();

				tinymce.execCommand('mceInsertContent', false, content);
			});

			// Add custom button and link to action
			ed.addButton('styled_button', {
				title : 'Ajouter un bouton',
				cmd : 'styled_button_action',
				image: url + '/button.png'
			});
		},
	});

	// Register our TinyMCE plugin
	// first parameter is the button ID1
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('styled_button', tinymce.plugins.styled_button);

});