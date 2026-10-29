
function playQuizSound(correct) {
    const AudioContextClass =
        window.AudioContext || window.webkitAudioContext;

    const audio = new AudioContextClass();
    const oscillator = audio.createOscillator();
    const volume = audio.createGain();

    oscillator.type = "sine";

    if (correct) {
        oscillator.frequency.setValueAtTime(600, audio.currentTime);
        oscillator.frequency.setValueAtTime(900, audio.currentTime + 0.15);
    } else {
        oscillator.frequency.setValueAtTime(350, audio.currentTime);
        oscillator.frequency.setValueAtTime(180, audio.currentTime + 0.15);
    }

    volume.gain.setValueAtTime(0.3, audio.currentTime);
    volume.gain.exponentialRampToValueAtTime(
        0.001,
        audio.currentTime + 0.35
    );

    oscillator.connect(volume);
    volume.connect(audio.destination);

    audio.resume().then(() => {
        oscillator.start();
        oscillator.stop(audio.currentTime + 0.35);
    });

    oscillator.onended = () => audio.close();
}
