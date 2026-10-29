
function playKeySound() {
    const AudioContextClass =
        window.AudioContext || window.webkitAudioContext;

    const audio = new AudioContextClass();
    const oscillator = audio.createOscillator();
    const volume = audio.createGain();

    oscillator.type = "sine";

    oscillator.frequency.setValueAtTime(650, audio.currentTime);
    oscillator.frequency.setValueAtTime(900, audio.currentTime + 0.12);

    volume.gain.setValueAtTime(0.3, audio.currentTime);
    volume.gain.exponentialRampToValueAtTime(
        0.001,
        audio.currentTime + 0.3
    );

    oscillator.connect(volume);
    volume.connect(audio.destination);

    audio.resume().then(() => {
        oscillator.start();
        oscillator.stop(audio.currentTime + 0.3);
    });

    oscillator.onended = () => audio.close();
}
