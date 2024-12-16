$(document).ready(function () {
    $(window).on('load', function () {
        const page = 'dashboard.php';
        const stateObj = {page: formatForUrl(page)};
        history.replaceState(stateObj, null, formatForUrl(page));
    })
    $('body').on('click', function (e) {
        const targetClass = $(e.target).attr('class');
        const targetId = $(e.target).attr('id');
        if (targetClass === 'no_refresh') {
            e.preventDefault();
            const page = $(e.target).attr('href');
            const stateObj = {page: formatForUrl(page)};
            history.pushState(stateObj, null, formatForUrl(page));

            // Load the page and put its contents in the main element.
            requestMainContent(page);

            $(window).on('popstate', function () {
                const page = history.state.page;
                const filename = page + '.php';

                // Load the page and put its contents in the main element.
                requestMainContent(filename);
            });
        } else if (targetClass === 'contactInfo') {
            e.preventDefault();
            const page = $(e.target).attr('href');
            const stateObj = {page: formatForUrl(page)};
            history.pushState(stateObj, null, formatForUrl(page));

            const contactName = $(e.target).attr('id');
            console.log(contactName);

            $.ajax(`view_contact_info.php?contactName=${contactName}`, {
                method: 'GET'
            }).done(response => requestContactInfo(response))
                .fail(() => alert('There was a problem with the request.'));

            $(window).on('popstate', function () {
                const page = history.state.page;
                const filename = page + '.php';

                // Load the page and put its contents in the main element.
                requestContactInfo(filename);
            });
        } else if (targetClass === 'cont_types') {
            e.preventDefault();
            const filter = $(e.target).attr('id');

            $.ajax(`dashboard_contacts.php?filter=${filter}`, {
                method: 'GET'
            }).done(response => requestHomeContent(response))
                .fail(() => alert('There was a problem with the request.'));

            removeActiveClass();
            $(e.target).parent().addClass('active');
        } else if (targetClass === 'assigned_to_me') {
            let assigned_to = $(e.target).attr('id');
            let contactName = $(e.target).attr('value');

            $.ajax(`view_contact_info.php?contactName=${contactName}`, {
                method: 'POST',
                data: {
                    assigned_to: assigned_to
                }
            }).done(response => requestContactInfo(response))
                .fail(() => alert('There was a problem with the request.'));
        } else if (targetClass === 'switch') {
            let type = $(e.target).attr('value');
            let contactName = $(e.target).attr('id');

            $.ajax(`view_contact_info.php?contactName=${contactName}`, {
                method: 'POST',
                data: {
                    type: type
                }
            }).done(response => requestContactInfo(response))
                .fail(() => alert('There was a problem with the request.'));
        } else if (targetId === 'addNote') {
            e.preventDefault();
            let comment = $('#comment').val();
            let contactName = $(e.target).attr('value');

            $.ajax(`view_contact_info.php?contactName=${contactName}`, {
                method: 'POST',
                data: {
                    comment: comment
                }
            }).done(response => requestContactInfo(response))
                .fail(() => alert('There was a problem with the request.'));
        }
    });
});
