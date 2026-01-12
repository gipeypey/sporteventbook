document.addEventListener("DOMContentLoaded", () => {
    const accordions = document.querySelectorAll(
        ".bg-white.rounded-2xl > .flex.items-center.justify-between"
    );

    accordions.forEach((header) => {
        const parent = header.parentElement;
        const icon = header.querySelector("img");

        const contents = [];
        let sibling = header.nextElementSibling;
        while (sibling) {
            contents.push(sibling);
            sibling = sibling.nextElementSibling;
        }

        header.addEventListener("click", () => {
            const isOpen = contents[0].style.display !== "none";

            contents.forEach((el) => {
                el.style.display = isOpen ? "none" : "block";
            });

            if (icon) {
                icon.style.transform = isOpen ? "rotate(0deg)" : "rotate(180deg)";
                icon.style.transition = "transform 0.3s ease";
            }
        });
    });
});
