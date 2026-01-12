document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('browse-search-btn');
    var bar = document.getElementById('browse-search-bar');
    var input = document.getElementById('browse-search-input');
    var clearBtn = document.getElementById('browse-search-clear');
    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-title]'));

    function filter(q) {
        var term = (q || '').trim().toLowerCase();
        cards.forEach(function (card) {
            var title = card.getAttribute('data-title') || '';
            var match = term === '' || title.indexOf(term) !== -1;
            card.style.display = match ? '' : 'none';
        });
        var leftCol = document.getElementById('left-col');
        var rightCol = document.getElementById('right-col');
        if (!leftCol || !rightCol) return;
        var visibleCards = cards.filter(function (c) {
            return c.style.display !== 'none';
        });
        if (visibleCards.length === 1) {
            var only = visibleCards[0];
            if (only.parentElement && only.parentElement.id === 'right-col') {
                leftCol.appendChild(only);
            }
        }
    }

    if (btn) {
        btn.addEventListener('click', function () {
            if (bar) {
                bar.classList.toggle('hidden');
                if (!bar.classList.contains('hidden') && input) {
                    input.focus();
                }
            }
        });
    }
    if (input) {
        input.addEventListener('keyup', function () {
            filter(input.value);
        });
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            input.value = '';
            filter('');
        });
    }
});
