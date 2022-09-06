import {scrollToError} from "@/Services/ValidationService";

const handleError = (error, scroll = true) => {
    console.error('Error has occurred', error);

    if (scroll) {
        scrollToError();
    }
};

export {handleError};
