$(document).ready(function() {
    $(".redigerbar").each(function() {
        var skin = "charcoal";
        if ($(".side:nth-child(2n)").find($(this)).length == 0) {
            skin = "lightgray";
        }
        var $editor = $(this);

        tinymce.init({
            selector: "#"+$(this).attr("id"),
            skin: skin,
            plugins: "link image imagetools",
            file_browser_callback_types: "image",
            file_browser_callback: function(field_name, url, type, win) {
                var $input = $("<input type='file' style='display: none;' />")
                    .insertAfter($editor);
                $input.change(function(event) {
                    win.document.getElementById(field_name).value = URL.createObjectURL(event.target.files[0]);
                });
                $input.trigger("click");
            },
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
                        var num_uploads = 0;
                        var uploads_completed = 0;
                        var error = false;

                        function uploadContents() {
                            if (!error && num_uploads == uploads_completed) {
                                // As .getContent() returnes a set of all the child elements of the
                                // editor we must append it to a dummy element to get the html content
                                var $content = $('<div />').append($(editor.getContent()).clone());
                                $content.find("img").each(function() {
                                    // Convert the image size to make it consistent across all screens
                                    // The width is stored as a percentage, so that it's tied to the width
                                    // of the screen. The height is stored as a percentage of the height,
                                    // which is applied through padding, to keep the aspect ratio.
                                    var width = (100 * $(this).width() / $editor.width()) + "%";
                                    var padding = ($(this).height() / $(this).width()) + "%";
                                    console.log("width: "+width+", padding: "+padding);
                                    $(this).css("width", width).css("padding-bottom", padding).css("min-width", "250px")
                                           .css("max-width", "100%").removeAttr("height").removeAttr("width");
                                });
                                var content = $content.html();

                                $.ajax("sider/intern/innhold.php", {
                                    method: "POST",
                                    dataType: "JSON",
                                    data: {navn: navn, innhold: content},
                                    success: function(data) {
                                        if (typeof data["status"] == "undefined") {
                                            error = true;
                                            if (typeof data["error"] == "undefined") {
                                                alert("Det har oppstått en feil. Ta kontakt med webkom.");
                                            } else {
                                                alert(data["error"]);
                                            }
                                        } else {
                                            btn.disabled(true);
                                        }
                                    },
                                    complete: function() {
                                        editor.setProgressState(false);
                                    }
                                });
                            }
                        }

                        $editor.find("img").each(function(i) {
                            var $img = $(this);
                            var src = $img.attr("src");
                            if (!error && src.startsWith("blob")) {
                                num_uploads += 1;
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', src, true);
                                xhr.responseType = 'blob';

                                xhr.onload = function(e) {
                                    if (this.status == 200) {
                                        var blob = this.response;
                                        var xhr, formData;

                                        xhr = new XMLHttpRequest();
                                        xhr.withCredentials = false;
                                        xhr.open('POST', 'upload_image_innhold.php');

                                        xhr.onload = function() {
                                            if (error) {
                                                return;
                                            }
                                            var json;

                                            if (xhr.status != 200) {
                                                error = true;
                                                console.log('HTTP Error: ' + xhr.status);
                                                alert("Det har oppstått en feil. Ta kontakt med webkom.");
                                                return;
                                            }

                                            json = JSON.parse(xhr.responseText);

                                            if (!json || typeof json.location != 'string') {
                                                editor.setProgressState(false);
                                                error = true;
                                                if (json.hasOwnProperty("error")) {
                                                    alert(json.error);
                                                } else {
                                                    alert("Det har oppstått en feil. Ta kontakt med webkom.");
                                                }
                                                return;
                                            }

                                            $img.attr("src", json.location);
                                            uploads_completed += 1;
                                            uploadContents();
                                        };

                                        formData = new FormData();
                                        formData.append('navn', navn);
                                        var blobname = "blob" + i;
                                        formData.append('blobname', blobname);
                                        formData.append(blobname, blob);

                                        xhr.send(formData);
                                    }
                                };

                                xhr.send();
                            }
                        }).promise().done(function() {
                            uploadContents();
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
