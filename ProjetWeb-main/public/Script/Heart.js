function toggleHeart(event, id) {
    const icon = document.getElementById("heart");
    if (icon.src.includes("bx-heart.svg")) {
        icon.src = "Bootstrap/images/bxs-heart.svg";
    } else {
        icon.src = "Bootstrap/images/bx-heart.svg";
    }
}