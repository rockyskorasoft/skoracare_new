import {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Font,
    Paragraph,
    SourceEditing,
    HtmlEmbed,
    GeneralHtmlSupport,
} from "ckeditor5";

if ($(".editor").length) {
    document.querySelectorAll(".editor").forEach((editorElement) => {
        ClassicEditor.create(editorElement, {
            licenseKey: "GPL",
            plugins: [
                Essentials,
                Bold,
                Italic,
                Font,
                Paragraph,
                SourceEditing,
                HtmlEmbed,
                GeneralHtmlSupport,
            ],
            toolbar: [
                "undo",
                "redo",
                "|",
                "bold",
                "italic",
                "|",
                "fontSize",
                "fontFamily",
                "fontColor",
                "fontBackgroundColor",
                "|",
                "sourceEditing",
                "|",
                "htmlEmbed",
            ],

            // Allow all HTML tags, attributes, classes, and inline styles
            htmlSupport: {
                allow: [
                    {
                        name: /.*/, // allow all elements
                        attributes: true,
                        classes: true,
                        styles: true,
                    },
                ],
            },

            htmlEmbed: {
                showPreviews: true,
            },
        })
            .then((newEditor) => {
                // Store instance
                window.editors = window.editors || [];
                window.editors.push(newEditor);
            })
            .catch((error) => console.error(error));
    });
}