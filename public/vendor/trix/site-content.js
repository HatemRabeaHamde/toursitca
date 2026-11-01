document.addEventListener('trix-before-initialize', () => {
    Trix.config.toolbar.getDefaultHTML = () => `<div class="trix-button-row"><span class="trix-button-group">
    <button type="button" class="trix-button" data-trix-attribute="bold" title="Bold">Bold</button>
    <button type="button" class="trix-button" data-trix-attribute="italic" title="Italic">Italic</button>
    <button type="button" class="trix-button" data-trix-attribute="href" data-trix-action="link" title="Link">Link</button>
    <button type="button" class="trix-button" data-trix-attribute="bullet" title="Bullet list">List</button>
    </span></div><div class="trix-dialogs" data-trix-dialogs><div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
    <label>Link URL <input type="url" data-trix-input name="href" placeholder="https://example.com" required></label>
    <button type="button" data-trix-method="setAttribute">Apply</button><button type="button" data-trix-method="removeAttribute">Remove link</button>
    </div></div>`;
});
document.addEventListener('trix-file-accept', event => event.preventDefault());
