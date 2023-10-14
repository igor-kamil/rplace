<template>
    <canvas width="100" height="100" ref="place" @click="canvasClicked" :style="`transform: scale(${SCALE})`"></canvas>
    <div class="absolute z-10 border-black border-2 shadow-[0_0_8px_4px_#000]" :style="overlayStyle" id="overlay" v-if="activePixel"></div>
</template>

<script setup>
    import { onMounted, ref, reactive } from 'vue';

    const SCALE = 16;
    const place = ref(null);
    const activePixel = ref(null);

    const colorOptions = {
        red: '231, 76, 60',
        orange: '230, 126, 34',
        yellow: '241, 196, 15',
        green: '46, 204, 113',
        blue: '52, 152, 219',
        purple: '155, 89, 182',
        white: '236, 240, 241',
        black: '44, 62, 80',
        key: function(n) {
            return this[Object.keys(this)[n]];
        }
    }

    const overlayStyle = reactive({top: '0px', left: '0px', width: SCALE + 'px', height: SCALE + 'px'});

    onMounted(() => {
        fetch('/map')
            .then(response => response.json())
            .then(data => {
                let ctx = place.value.getContext('2d');
                data.forEach((column, x) => {
                    column.forEach((pixel, y) => {
                        let [colorIndex, email] = pixel.split(":");
                        ctx.fillStyle = 'rgb('+colorOptions.key(colorIndex)+')';
                        ctx.fillRect(x,y, 1, 1)
                    });
                });
            });
    });

    const canvasClicked = () => {
        let clickedX = Math.floor(event.pageX / SCALE);
        let clickedY = Math.floor(event.pageY / SCALE);
        activePixel.value = `${clickedX}:${clickedY}`;
        overlayStyle.top = (clickedY * SCALE) + 'px';
        overlayStyle.left = (clickedX * SCALE) + 'px';
        activePixel.value = `${clickedX}:${clickedY}`;
    }
</script>

<style>
    canvas {
        image-rendering: pixelated;
        transform-origin: top left;
    }
</style>