



document.addEventListener( 'click', (e) => 
{
    if( e.target.classList.contains( "ydgdict_glossary_get_description" ) )
    {
        e.preventDefault();

        const desc_container = e.target.parentNode.parentNode;
        const description = desc_container.querySelector( "div.ydgdict_glossary_description" );
        const image = desc_container.querySelector( "img.ydgdict_desc_img" );

        desc_container.classList.toggle("ydgdict_clicked");
        description.classList.toggle("ydgdict_hideme");
        
        if ( image ) image.setAttribute( 'src', image.getAttribute( 'data-src') );
    }
});