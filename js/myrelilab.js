jQuery('.edit-post-header > div:first-child  .components-button').ready(function($){

    //wp.domReady(e=> {


        setTimeout(e=>{
            console.log('relilab', wp.data.select('core/editor').getCurrentPost());

            if (wp.data.select('core/editor').getCurrentPost().type == 'material') {

                console.log('editor_loaded');

                jQuery('.edit-post-header > div:first-child .components-button').attr('href', wp.data.select('core/editor').getPermalink());
                jQuery('.edit-post-header > div:first-child .components-button').click(e => {
                    location.href = wp.data.select('core/editor').getPermalink();
                    return false;
                });
            }

        },10);


    //})
});
