export default {
    mounted(el, binding) {
        el.addEventListener('keydown', (e) => {
            // delete, backpsace, tab, escape, enter,
            let special = [46, 8, 9, 27, 13];

            if (binding.modifiers['comma']) {
                special.push(188) // comma
            }

            if (binding.modifiers['space']) {
                special.push(32) // space
            }

            if (binding.modifiers['dot']) {
                special.push(110, 190) // decimal(numpad), dot
            }

            if (binding.modifiers['minus']) {
                special.push(189, 109) // minus(numpad), minus
            }

            // special from above
            if (special.indexOf(e.keyCode) !== -1 ||
                // Ctrl+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Ctrl+C
                (e.keyCode === 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Ctrl+X
                (e.keyCode === 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Shift
                (e.keyCode >= 48 && e.keyCode <= 57 && e.shiftKey === true) ||
                // home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return // allow
            }
            // max chars
            if (Object.keys(binding.modifiers).some(k => k.includes('max-'))) {
                const prop = Object.keys(binding.modifiers).find(k => k.includes('max-'));
                const parts = prop.split('-');

                if (String(e.target.value).length >= parts[1] && e.srcElement.selectionStart === e.srcElement.selectionEnd) {
                    return e.preventDefault();
                }
            }

            if ((binding.modifiers['alpha']) &&
                // a-z/A-Z
                (e.keyCode >= 65 && e.keyCode <= 90)) {
                return // allow
            }
            if ((binding.modifiers['number']) &&
                // number keys without shift
                ((!e.shiftKey && (e.keyCode >= 48 && e.keyCode <= 57)) ||
                    // numpad number keys
                    (e.keyCode >= 96 && e.keyCode <= 105))) {
                return // allow
            }

            // otherwise, stop the keystroke
            e.preventDefault();
        });
    }
};
