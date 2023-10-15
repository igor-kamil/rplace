<template>
    <canvas width="100" height="100" ref="place" @click="canvasClicked" :style="`transform: scale(${SCALE})`"></canvas>
    <div
        class="absolute z-10 border-black border-2 shadow-[0_0_8px_4px_#000]"
        :style="overlayStyle"
        id="overlay"
        v-if="activePixel"
    ></div>
    <div id="colorPicker" class="fixed bg-white bottom-0 left-0 w-full p-1 text-center" v-if="activePixel">
        <p class="mb-1">
            Last changed by: <strong>{{ activePixelEmail }}</strong>
        </p>
        <nav>
            <button
                v-for="color in Object.keys(colorOptions)"
                :key="color"
                class="w-16 h-8 border-white border-2 rounded-sm mx-1 pointer hover:opacity-80"
                :class="color"
                @click="changePixel(color)"
                :style="{ background: 'rgb(' + colorOptions[color] + ')' }"
            ></button>
        </nav>
    </div>
</template>

<script setup>
import { onMounted, ref, reactive, computed } from "vue";

const SCALE = 16;
const place = ref(null);
const activePixel = ref(null);
const map = ref([]);

const colorOptions = {
    red: "231, 76, 60",
    orange: "230, 126, 34",
    yellow: "241, 196, 15",
    green: "46, 204, 113",
    blue: "52, 152, 219",
    purple: "155, 89, 182",
    white: "236, 240, 241",
    black: "44, 62, 80",
    key: function (n) {
        return this[Object.keys(this)[n]];
    },
};

const overlayStyle = reactive({
    top: "0px",
    left: "0px",
    width: SCALE + "px",
    height: SCALE + "px",
});

onMounted(() => {
    fetch("/map")
        .then((response) => response.json())
        .then((data) => {
            map.value = data;
            let ctx = place.value.getContext("2d");
            data.forEach((column, x) => {
                column.forEach((pixel, y) => {
                    let [colorIndex, email] = pixel.split(":");
                    ctx.fillStyle = "rgb(" + colorOptions.key(colorIndex) + ")";
                    ctx.fillRect(x, y, 1, 1);
                });
            });
        });
});

const canvasClicked = () => {
    let clickedX = Math.floor(event.pageX / SCALE);
    let clickedY = Math.floor(event.pageY / SCALE);
    activePixel.value = `${clickedX}:${clickedY}`;
    overlayStyle.top = clickedY * SCALE + "px";
    overlayStyle.left = clickedX * SCALE + "px";
    activePixel.value = `${clickedX}:${clickedY}`;
};

const activePixelEmail = computed(() => {
    if (!activePixel.value) {
        return "";
    }

    let [x, y] = activePixel.value.split(":");
    return map.value[x][y].split(":")[1];
});

const changePixel = (color) => {
    let ctx = place.value.getContext("2d");
    ctx.fillStyle = "rgb(" + colorOptions[color] + ")";

    let [x, y] = activePixel.value.split(":");
    ctx.fillRect(x, y, 1, 1);

    fetch("/save", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accepts: "application/json",
        },
        body: JSON.stringify({
            key: activePixel.value,
            color: Object.keys(colorOptions).indexOf(color),
        }),
    });
};
</script>

<style>
canvas {
    image-rendering: pixelated;
    transform-origin: top left;
}
</style>
