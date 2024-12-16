/**
 * Format page filename to get the page name
 *
 * @param page string the path to the page with an extension.
 * @return string
 */
function formatForUrl(page) {
    const pageName = page.split('.');
    return pageName[0];
}

/**
 * Load the page and put its contents in the main element.
 *
 * @param content
 */
const requestHomeContent = (content) => $('#homeResult').html(content);

/**
 * Load the page and put its contents in the main element.
 *
 * @param content
 */
const requestMainContent = (content) => $('#results').load(content);

/**
 * Load the page and put its contents in the main element.
 *
 * @param contactInfo
 */
const requestContactInfo = (contactInfo) => {
    $('#results').html(contactInfo)
};

const removeActiveClass = () => $('.active').removeClass('active');