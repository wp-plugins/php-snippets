(function() {

    tinymce.create('tinymce.plugins.php_snippets', {

        init : function(ed, url){
            ed.addButton('php_snippets', {
                icon: 'php-snippets-icon',
                title : 'Insert PHP snippet',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        show_php_snippets() // <-- you must create this JS function!
                        );
                }
            });
        },

        getInfo : function() {
            return {
                longname : 'PHP Snippets',
                author : 'Everett Griffiths',
                authorurl : 'http://craftsmancoding.com',
                infourl : '',
                version : "0.5"
            };
        }
    });

    tinymce.PluginManager.add('php_snippets', tinymce.plugins.php_snippets);
    
})();