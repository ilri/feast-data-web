/**
 * Abstracting UI strings here to aid future localization
 */
var stringSystemError = "There seems to be a problem with the system.  Try again or contact the administrator via the 'Help' link.";

// TODO: Consider whether it's better to grab these from the database via a System API call.
var userStatusOptions = [
    {title:'Unconfirmed', status_id :1},
    {title:'Active', status_id :3},
    {title:'Inactive', status_id :4}
];

var userRoleOptions = [
    {title:'User', role_id :0},
    {title:'Administrator', role_id :1},
];
