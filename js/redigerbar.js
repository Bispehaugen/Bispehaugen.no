$(document).ready(function() {
    $(".redigerbar").each(function() {
        var skin = "lightgray";
        if ($(".side:nth-child(even)").find($(this)).length != 0) {
            skin = "charcoal";
        }

        tinymce.init({
            selector: "#"+$(this).attr("id"),
            skin: skin,
            plugins: "link",
            language: "nb_NO",
            inline: true,
            menu: {
                edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
                insert: {title: 'Insert', items: 'link media | template hr'},
                format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            },
            toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | lagre | ferdig",
            setup: function(editor) {
                var locked = true;
                var $editor = $("#"+editor.id);

                editor.setMode("readonly");
                
                $editor.dblclick(function() {
                    if (locked) {
                        if (window.getSelection) {
                            window.getSelection().removeAllRanges();
                        } else if (document.selection) {
                            document.selection.empty();
                        }
                        editor.setMode("design");
                        editor.fire("activate");
                        locked = false;
                    }
                });

                editor.addButton("lagre", {
                    text: "Lagre",
                    disabled: true,
                    onclick: function() {
                        editor.setProgressState(true);
                        var btn = this;
                        var navn = $(editor.getElement()).data("navn");
                        var content = editor.getContent();
                        $.ajax("sider/intern/innhold.php", {
                            method: "POST",
                            data: {navn: navn, innhold: content},
                            success: function() {
                                btn.disabled(true);
                            },
                            complete: function() {
                                editor.setProgressState(false);
                            }
                        });
                    },
                    onpostrender: function() {
                        var btn = this;
                        editor.on("change", function(e) {
                            btn.disabled(false);
                        });
                    }
                });

                editor.addButton("ferdig", {
                    text: "Ferdig",
                    onclick: function() {
                        editor.setMode("readonly");
                        editor.fire("deactivate");
                        locked = true;
                    }
                });
            }
        });
    });
});
