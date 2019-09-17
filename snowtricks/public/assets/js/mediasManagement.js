let $videosCollectionHolder;
const $addVideoButton = $('<button type="button" class="btn btn-success"><i class="fa fa-plus"></button>');
const $newAddVideoButton = $('<div class="video-button"></div>').append($addVideoButton);

let $imagesCollectionHolder;
const $addImageButton = $('<button type="button" class="btn btn-success"><i class="fa fa-plus"></button>');
const $newAddImageButton = $('<div class="image-button"></div>').append($addImageButton);

jQuery(document).ready(function() {

    $videosCollectionHolder = $('#video');
    $videosCollectionHolder.find('.videoContent').each(function() {
        addVideoFormDeleteLink($(this));
    });
    $videosCollectionHolder.append($newAddVideoButton);

    $videosCollectionHolder.data('index', $videosCollectionHolder.find(':input').length);
    $addVideoButton.on('click', function() {
        addVideoForm($videosCollectionHolder, $newAddVideoButton);
    });

    $imagesCollectionHolder = $('#image');
    $imagesCollectionHolder.find('.imageContent').each(function() {
        addImageFormDeleteLink($(this));
    });
    $imagesCollectionHolder.append($newAddImageButton);
    $imagesCollectionHolder.data('index', $imagesCollectionHolder.find(':input').length);
    $addImageButton.on('click', function() {
        addImageForm($imagesCollectionHolder, $newAddImageButton);
    });

});

function addVideoForm($collectionHolder, $newLinkLi) {
    const prototype = $collectionHolder.data('prototype');
    const index = $collectionHolder.data('index');
    let newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);

    const $newForm = $('<div class="video-form"></div>').append(newForm);

    $newLinkLi.before($newForm);
    addVideoFormDeleteLink($newForm);
}
function addVideoFormDeleteLink($tagForm) {
    const $removeFormButton = $('<button type="button" class="btn btn-danger"><i class="fa fa-times"></i></button>');

    $tagForm.append($removeFormButton);

    $removeFormButton.on('click', function() {
        $tagForm.remove();
    });
}

function addImageForm($collectionHolder, $newLink) {
    const prototype = $collectionHolder.data('prototype');
    const index = $collectionHolder.data('index');
    let newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);

    const $newForm = $('<div class="image-form"></div>').append(newForm);

    $newLink.before($newForm);
    addVideoFormDeleteLink($newForm);
}

function addImageFormDeleteLink($tagForm) {
    const $removeFormButton = $('<button type="button" class="btn btn-danger"><i class="fa fa-times"></button>');

    $tagForm.append($removeFormButton);

    $removeFormButton.on('click', function() {
        $tagForm.remove();
    });
}

