
function playWinSound() {
    const AudioContextClass =
        window.AudioContext || window.webkitAudioContext;

    const audio = new AudioContextClass();

    audio.resume().then(() => {
        const notes = [523, 659, 784, 1047];

        notes.forEach((frequency, index) => {
            const oscillator = audio.createOscillator();
            const volume = audio.createGain();

            const start = audio.currentTime + index * 0.2;

            oscillator.type = "sine";
            oscillator.frequency.value = frequency;

            volume.gain.setValueAtTime(0.001, start);
            volume.gain.linearRampToValueAtTime(0.3, start + 0.03);
            volume.gain.exponentialRampToValueAtTime(
                0.001,
                start + 0.3
            );

            oscillator.connect(volume);
            volume.connect(audio.destination);

            oscillator.start(start);
            oscillator.stop(start + 0.3);
        });

        setTimeout(() => audio.close(), 1500);
    });
}
