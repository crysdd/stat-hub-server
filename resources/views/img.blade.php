<script>
    new Image().src = "/hit?r=" + escape(document.referrer) + ((typeof(screen) == "undefined") ? "" : "&s=" + screen
            .width + "*" + screen.height + "*" + (screen.colorDepth ? screen.colorDepth : screen.pixelDepth)) + "&u=" +
        escape(document.URL) + "&h=" + escape(document.title.substring(0, 150)) + "&i=" + Math.random();
</script>
