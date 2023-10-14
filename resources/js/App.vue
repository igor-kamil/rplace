<template>
    <canvas width="100" height="100" ref="place"></canvas>
</template>

<script setup>
    import { onMounted, ref } from 'vue';

    const place = ref(null);

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

    onMounted(() => {
        fetch('/map')
            .then(response => response.json())
            .then(data => {
                let ctx = place.value.getContext('2d');
                data.forEach((column, x) => {
                    column.forEach((pixel, y) => {
                        let [colorIndex, email] = pixel.split(":")
                        ctx.fillStyle = 'rgb('+colorOptions.key(colorIndex)+')';
                        ctx.fillRect(x,y, 1, 1)
                    })
                });
            });
        
        // const canvas = refs.place;
        // const ctx = canvas.getContext('2d');
        // ctx.fillStyle = 'red';
        // ctx.fillRect(10, 10, 100, 100);
    });
</script>

<style>
    canvas {
        image-rendering: pixelated;
        transform: scale(16);
        transform-origin: top left;
    }
</style>