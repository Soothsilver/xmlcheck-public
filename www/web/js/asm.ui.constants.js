asm = window.asm || {};
asm.ui = window.asm.ui || {};
/**
 * Defines the minimum and maximum username and password length.
 * All other fields have a minimum length of 1 and no maximum length.
 * @type {{passwordMinLength: number, passwordMaxLength: number, usernameMinLength: number, usernameMaxLength: number}}
 */
asm.ui.constants = {
    passwordMinLength: 5,
    passwordMaxLength: 200,
    usernameMinLength: 5,
    usernameMaxLength: 200
};