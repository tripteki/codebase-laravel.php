"use strict";

function initWysiwygEditor(editorId, inputId, initialContent)
{
    initialContent = initialContent || "";

    if (! window.tiptap || ! window.tiptap.Editor) {

        return null;
    }

    var Editor = window.tiptap.Editor;
    var StarterKitExt = null;

    if (window.StarterKit) {

        StarterKitExt = window.StarterKit.default || window.StarterKit;
    }

    if (! StarterKitExt) {

        return null;
    }

    var editorElement = document.getElementById(editorId);
    var hiddenInput = document.getElementById(inputId);

    if (! editorElement || ! hiddenInput) {

        return null;
    }

    if (editorElement.editorInstance) {

        return editorElement.editorInstance;
    }

    var TextAlignExt = null;

    if (window.TextAlign) {

        TextAlignExt = window.TextAlign.default || window.TextAlign;
    }

    var UnderlineExt = null;

    if (window.Underline) {

        UnderlineExt = window.Underline.default || window.Underline;
    }

    var extensions = [StarterKitExt];

    if (UnderlineExt) {

        extensions.push(UnderlineExt);
    }

    if (TextAlignExt) {

        extensions.push(TextAlignExt.configure({

            types: ["heading", "paragraph"],
        }));
    }

    var editor = new Editor({
        element: editorElement,
        extensions: extensions,
        content: initialContent,
        editorProps: {

            attributes: {

                class: "prose prose-sm max-w-none p-4 min-h-[200px] focus:outline-none dark:prose-invert",
            },
        },

        onUpdate: function (data)
        {
            var html = data.editor.getHTML();

            hiddenInput.value = html;
            hiddenInput.dispatchEvent(new Event("input", { bubbles: true, }));
            hiddenInput.dispatchEvent(new Event("change", { bubbles: true, }));
        },
    });

    var toolbar = editorElement.closest(".wysiwyg-container");

    if (toolbar) {

        toolbar.querySelectorAll("[data-wysiwyg-action]").forEach(function (button)
        {
            button.addEventListener("click", function (e)
            {
                e.preventDefault();

                var action = this.dataset.wysiwygAction;
                var level = this.dataset.level;

                editor.chain().focus();

                switch (action) {
                    case "bold":
                        editor.chain().focus().toggleBold().run();
                        break;
                    case "italic":
                        editor.chain().focus().toggleItalic().run();
                        break;
                    case "underline":
                        if (editor.chain().focus().toggleUnderline) {
                            editor.chain().focus().toggleUnderline().run();
                        }
                        break;
                    case "strike":
                        editor.chain().focus().toggleStrike().run();
                        break;
                    case "code":
                        editor.chain().focus().toggleCode().run();
                        break;
                    case "codeBlock":
                        editor.chain().focus().toggleCodeBlock().run();
                        break;
                    case "bulletList":
                        editor.chain().focus().toggleBulletList().run();
                        break;
                    case "orderedList":
                        editor.chain().focus().toggleOrderedList().run();
                        break;
                    case "blockquote":
                        editor.chain().focus().toggleBlockquote().run();
                        break;
                    case "heading":
                        if (level) {

                            editor.chain().focus().toggleHeading({ level: parseInt(level), }).run();
                        }
                        break;
                    case "alignLeft":
                        editor.chain().focus().setTextAlign("left").run();
                        break;
                    case "alignCenter":
                        editor.chain().focus().setTextAlign("center").run();
                        break;
                    case "alignRight":
                        editor.chain().focus().setTextAlign("right").run();
                        break;
                    case "link":
                        var url = window.prompt("Enter URL:", "https://");
                        if (url) {

                            editor.chain().focus().toggleLink({ href: url, }).run();
                        }
                        break;
                    case "removeLink":
                        editor.chain().focus().unsetLink().run();
                        break;
                }

                updateToolbarState(editor, toolbar);
            });
        });
    }

    function updateToolbarState(editor, toolbar)
    {
        toolbar.querySelectorAll("[data-wysiwyg-action]").forEach(function (button)
        {
            var action = button.dataset.wysiwygAction;
            var level = button.dataset.level;
            var isActive = false;

            switch (action) {
                case "bold":
                    isActive = editor.isActive("bold");
                    break;
                case "italic":
                    isActive = editor.isActive("italic");
                    break;
                case "underline":
                    isActive = editor.isActive("underline");
                    break;
                case "strike":
                    isActive = editor.isActive("strike");
                    break;
                case "code":
                    isActive = editor.isActive("code");
                    break;
                case "codeBlock":
                    isActive = editor.isActive("codeBlock");
                    break;
                case "bulletList":
                    isActive = editor.isActive("bulletList");
                    break;
                case "orderedList":
                    isActive = editor.isActive("orderedList");
                    break;
                case "blockquote":
                    isActive = editor.isActive("blockquote");
                    break;
                case "heading":
                    if (level) {

                        isActive = editor.isActive("heading", { level: parseInt(level), });
                    }
                    break;
                case "alignLeft":
                    isActive = editor.isActive({ textAlign: "left", });
                    break;
                case "alignCenter":
                    isActive = editor.isActive({ textAlign: "center", });
                    break;
                case "alignRight":
                    isActive = editor.isActive({ textAlign: "right", });
                    break;
            }

            if (isActive) {

                button.classList.add("bg-gray-200", "dark:bg-gray-600");
                button.classList.remove("hover:bg-gray-100", "dark:hover:bg-gray-600");

            } else {

                button.classList.remove("bg-gray-200", "dark:bg-gray-600");
                button.classList.add("hover:bg-gray-100", "dark:hover:bg-gray-600");
            }
        });
    }

    editor.on("selectionUpdate", function ()
    {
        if (toolbar) {

            updateToolbarState(editor, toolbar);
        }
    });

    editor.on("transaction", function ()
    {
        if (toolbar) {

            updateToolbarState(editor, toolbar);
        }
    });

    editorElement.editorInstance = editor;

    return editor;
}

function initWysiwygEditors()
{
    if (! window.tiptap || ! window.tiptap.Editor || ! window.StarterKit) {

        setTimeout(initWysiwygEditors, 100);
        return;
    }

    document.querySelectorAll(".wysiwyg-container").forEach(function (container)
    {
        var editorId = container.dataset.wysiwygEditor;
        var inputId = container.dataset.wysiwygInput;

        if (! editorId) {

            var editorElement = container.querySelector("[id$=\"-editor\"]");
            editorId = editorElement ? editorElement.id : null;
        }

        if (! inputId) {

            var inputElement = container.querySelector("[id$=\"-input\"]");
            inputId = inputElement ? inputElement.id : null;
        }

        if (! editorId || ! inputId) {

            return;
        }

        var editorElement = document.getElementById(editorId);
        var hiddenInput = document.getElementById(inputId);

        if (editorElement && hiddenInput && ! editorElement.editorInstance) {

            var initialContent = hiddenInput.value || "";

            initWysiwygEditor(editorId, inputId, initialContent);
        }
    });
}

function waitForTipTap()
{
    if (window.tiptapReady === true || (window.tiptap && window.tiptap.Editor && window.StarterKit)) {

        setTimeout(function ()
        {
            initWysiwygEditors();
        }, 200);

    } else {
        setTimeout(waitForTipTap, 100);
    }
}

function ensureWysiwygEditors()
{
    if (! window.tiptap || ! window.tiptap.Editor || ! window.StarterKit) {

        return;
    }

    document.querySelectorAll(".wysiwyg-container").forEach(function (container)
    {
        var editorId = container.dataset.wysiwygEditor;
        var inputId = container.dataset.wysiwygInput;

        if (! editorId) {

            var editorEl = container.querySelector("[id$=\"-editor\"]");

            editorId = editorEl ? editorEl.id : null;
        }

        if (! inputId) {

            var inputEl = container.querySelector("[id$=\"-input\"]");

            inputId = inputEl ? inputEl.id : null;
        }

        if (! editorId || ! inputId) {

            return;
        }

        var editorElement = document.getElementById(editorId);
        var hiddenInput = document.getElementById(inputId);

        if (! editorElement || ! hiddenInput) {

            return;
        }

        if (editorElement.editorInstance) {

            var newContent = hiddenInput.value || "";
            var currentContent = editorElement.editorInstance.getHTML();

            if (newContent !== currentContent && newContent !== "<p></p>") {

                editorElement.editorInstance.commands.setContent(newContent);
            }
        } else {

            initWysiwygEditor(editorId, inputId, hiddenInput.value || "");
        }
    });
}

if (document.readyState === "loading") {

    document.addEventListener("DOMContentLoaded", waitForTipTap);

} else {

    waitForTipTap();
}

if (typeof Livewire !== "undefined") {

    Livewire.hook("mounted", function ()
    {
        setTimeout(waitForTipTap, 300);
    });

    Livewire.hook("updated", function ()
    {
        setTimeout(ensureWysiwygEditors, 0);
    });

    Livewire.hook("morphed", function ()
    {
        requestAnimationFrame(function ()
        {
            ensureWysiwygEditors();
        });
    });
}
