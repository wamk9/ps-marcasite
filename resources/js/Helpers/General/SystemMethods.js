export default {
    dateStringToDb: (date) => {
        const dateSplitted = date.split('/');
        const dateSetted = new Date(dateSplitted[2], dateSplitted[1] - 1, dateSplitted[0]);

        let day = '' + dateSetted.getDate();
        let month = '' + (dateSetted.getMonth() + 1);
        let year = dateSetted.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, day, month].join('-');
    },
    dateDbToDateObject: (date) => {
        if (typeof date != 'string')
            return null;
        const dateSplitted = date.split('-');

        if (dateSplitted.length != 3 && isNaN(Date.parse(dateSplitted[0], dateSplitted[2] - 1, dateSplitted[1])))
            return null;

        return new Date(dateSplitted[0], dateSplitted[2] - 1, dateSplitted[1]);
    },
    dateStringToDateObject: (date) => {
        if (typeof date != 'string')
            return null;

        const dateSplitted = date.split('/');

        if (dateSplitted.length != 3 && isNaN(Date.parse(dateSplitted[2], dateSplitted[1] - 1, dateSplitted[0])))
            return null;

        return new Date(dateSplitted[2], dateSplitted[1] - 1, dateSplitted[0]);
    }
};
