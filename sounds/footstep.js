
function playFootstep() {
    const AudioContextClass =
        window.AudioContext || window.webkitAudioContext;

    const audio = new AudioContextClass();
    const oscillator = audio.createOscillator();
    const volume = audio.createGain();

    oscillator.type = "sine";
    oscillator.frequency.value = 350;

    volume.gain.value = 0.3;

    oscillator.connect(volume);
    volume.connect(audio.destination);

    audio.resume().then(() => {
        oscillator.start();
        oscillator.stop(audio.currentTime + 0.15);
    });

    oscillator.onended = () => audio.close();
}
