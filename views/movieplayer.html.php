<?php defined("SYSPATH") or die("No direct script access.") ?>

<script type='text/javascript' src='<?= url::abs_file("modules/g3jwplayer/libraries/player/jwplayer.js") ?>'></script>

<div id='<?= $attrs["id"] ?>'></div>
<script type="text/javascript">

    $(document).ready(function() {
        var videoUrl = '<?= $item->file_url(true) ?>';
        
        /*
        	With the help of the transcode-module it is possible to play
        	the "smallest" video resolution by default
        	
        	TODO: make this configurable
        */
        if ($('#resolution-select').length) {
            var fpBaseUrl = "<?php echo url::abs_file("var/modules/transcode/flv/" . $item->id); ?>/";
            var format = '<?= module::get_var("transcode", "format") ?>';
            
            var res = $('#resolution-select option:eq(1)').val();
            
            $('#resolution-select option:eq(1)').attr('selected', 'selected');

            videoUrl =  fpBaseUrl + res + "." + format;
        }
        
        jwplayer('<?= $attrs["id"] ?>').setup({
                    file		: videoUrl,
                    wmode   	: 'opaque',
                    autoplay: true,
                    width   : 600,
                    height  : 500,
                    flashplayer	: '<?= url::abs_file("lib/jwplayer/player.swf") ?>',
                    modes: [
                            {type: 'html5'},
                            {type: 'flash', src: '<?= url::abs_file("lib/jwplayer/player.swf") ?>'}
                    	],
                    clip: {
                        scaling: "fit"
                    }
        });
    
        /*
        	If the transcode module is installed the changeVideo() JS-function must be overwritten 
        	-> call the JW Player instead of the Flowplayer
        */
        
        if (!window.changeVideo) {
        	return;
        }
        
        window.changeVideo = function(res) {
 			if (res == "1") {
			    // plays the orignal resoluton -> player must change eventualy from HTML5 to flv 
			    // thus it needs a hard reset
			    var players = jwplayer.getPlayers();
			    
			    jQuery.each(players, function(i, v){
			        jwplayer.api.destroyPlayer(v.id);
			    })
			    
			    jwplayer('g-item-id-<?php echo $item->id; ?>').setup({
			    	file		: '<?= $item->file_url(true) ?>',	
			    	wmode		: 'opaque',
			    	autoplay	: true,
			    	modes: [
			    		{type: 'html5'},
			    		{type: 'flash', src: '<?= url::abs_file("lib/jwplayer/player.swf") ?>'}
			    	],
			    	width	: 600,
			    	height	: 500
			    });
			} else {
			    jwplayer().load(fpBaseUrl + res + ".<?php echo module::get_var("transcode", "format")?>");
			}
        }
    });
</script>