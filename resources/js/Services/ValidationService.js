const scrollToError = () => {
    setTimeout(() => {
        const errors = document.querySelectorAll('.validation-error');

        if (errors.length > 0) {
            window.scroll({ top: (errors[0].parentNode.offsetTop - 60), left: 0, behavior: 'smooth' });
        }
    }, 100);
};

export {scrollToError};
