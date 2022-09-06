import printJs from 'print-js'
import EventBus from '@/Services/EventBus'

class Print {
    documentPdf(invoiceId) {
        printJs({
            printable: route('documents.pdf', ['invoices', invoiceId]),
            type: 'pdf',
            onLoadingStart: () => EventBus.emit('loader:show'),
            onLoadingEnd: () => setTimeout(() => EventBus.emit('loader:hide'), 500),
        });
    }

    mergedDocumentsPdf(periods, config = {}) {
        printJs({
            printable: route('tools.print.invoices.merged', {periods}),
            type: 'pdf',
            onLoadingStart: () => EventBus.emit('loader:show'),
            onLoadingEnd: () => {
                setTimeout(() => EventBus.emit('loader:hide'), 500);
                (config.onSuccess && config.onSuccess());
            },
            onPrintDialogClose: () => {
                (config.onPrintDialogClose && config.onPrintDialogClose());
            },
        });
    }
}

export default new Print;
