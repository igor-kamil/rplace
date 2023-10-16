<template>
    <canvas width="100" height="100" ref="place" @click="canvasClicked" :style="`transform: scale(${scale})`"></canvas>
    <div
        class="absolute z-10 border-black border-2 shadow-[0_0_8px_4px_#000]"
        :style="{ width: scale + 'px', height: scale + 'px', top: activeTop, left: activeLeft }"
        id="overlay"
        v-if="activePixel"
    ></div>
    <div id="colorPicker" class="fixed bg-white bottom-0 left-0 w-full p-1 text-center flex px-4" v-if="activePixel">
        <div class="flex-1">
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

        <div class="px-4">
            <label for="scale-range" class="block mb-1 ">Scale:</label>
            <input
                id="scale-range"
                type="range"
                step="1"
                min="10"
                max="24"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                v-model="scale"
            />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, reactive, computed } from "vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

const SCALE = 16;
const place = ref(null);
const activePixel = ref(null);
const map = ref([]);
const scale = ref(SCALE);

const colorOptions = {
    white: "236, 240, 241",
    red: "231, 76, 60",
    orange: "230, 126, 34",
    yellow: "241, 196, 15",
    green: "46, 204, 113",
    blue: "52, 152, 219",
    purple: "155, 89, 182",
    black: "44, 62, 80",
    key: function (n) {
        return this[Object.keys(this)[n]];
    },
};

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

    let echo = new Echo({
        broadcaster: "pusher",
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        wsHost: import.meta.env.VITE_PUSHER_HOST,
        wsPort: import.meta.env.VITE_PUSHER_PORT,
        wssPort: import.meta.env.VITE_PUSHER_PORT,
        forceTLS: false,
        encrypted: true,
        disableStats: true,
        enabledTransports: ["ws", "wss"],
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    });

    echo.channel("changes").listen("ColorChanged", (e) => {
        // console.log(e.change);
        let ctx = place.value.getContext("2d");
        let [colorIndex, email] = e.change.color.split(":");

        ctx.fillStyle = "rgb(" + colorOptions[Object.keys(colorOptions)[colorIndex]] + ")";

        let [x, y] = e.change.key.split(":");
        ctx.fillRect(x, y, 1, 1);

        map.value[x][y] = `${colorIndex}:${email}`;
    });
});

const canvasClicked = () => {
    let clickedX = Math.floor(event.pageX / scale.value);
    let clickedY = Math.floor(event.pageY / scale.value);
    activePixel.value = `${clickedX}:${clickedY}`;
    activePixel.value = `${clickedX}:${clickedY}`;
};

const activePixelEmail = computed(() => {
    if (!activePixel.value) {
        return "";
    }

    let [x, y] = activePixel.value.split(":");
    return map.value[x][y].split(":")[1];
});


const activeTop = computed(() => {
    return (activePixel.value) ? parseInt(activePixel.value.split(":")[1]) * scale.value + "px" : 0;
});

const activeLeft = computed(() => {
    return (activePixel.value) ? parseInt(activePixel.value.split(":")[0]) * scale.value + "px" : 0;
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
