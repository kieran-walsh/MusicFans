var bookmarkBtn = document.querySelector('#bookmarks-btn');
var postsBtn = document.querySelector('#posts-btn');
var contentBtn = document.querySelector('#content-btn');

bookmarkBtn.addEventListener("click", showBookmarks);
postsBtn.addEventListener("click", showPosts);
contentBtn.addEventListener("click", showRelContent);

var bookmarks = document.querySelector("#bookmarks");
var posts = document.querySelector("#recent-activity");
var relatedContent = document.querySelector("#related-content");

function showBookmarks() {
    bookmarks.style.display = "block";
    posts.style.display = "none";
    relatedContent.style.display = "none";
}

function showPosts() {
    bookmarks.style.display = "none";
    posts.style.display = "block";
    relatedContent.style.display = "none";
}

function showRelContent() {
    bookmarks.style.display = "none";
    posts.style.display = "none";
    relatedContent.style.display = "block";
}
