if (cookies.exists('language'))
{
    var displayLanguage = $.cookie('language');
    // Default is Czech language.
    if (displayLanguage !== 'en')
    {
        asm.lang = $.extend(true, asm.lang, asm.otherlangs.cs);
    }
};

asm.lang.setLanguage = function(languageCode) {
  cookies.set('language', languageCode);
};