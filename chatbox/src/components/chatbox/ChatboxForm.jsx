import { IoIosSend } from "react-icons/io";
import { IoMdImage } from "react-icons/io";
import { BsFillMicFill } from "react-icons/bs";
import { FaStop } from "react-icons/fa";
import { FaRegSmile } from "react-icons/fa";
import { useState, useRef, useEffect } from "react";
import emojiList from "../../assets/emoji.js";

const ChatboxForm = ({ input, setInput, handleSubmit, handleImage, image, fileInputRef }) => {
    const [isRecording, setIsRecording] = useState(false);
    const [audioBlob, setAudioBlob] = useState(null);
    const mediaRecorderRef = useRef(null);
    const audioChunksRef = useRef([]);
    const [recordingTime, setRecordingTime] = useState(0);
    const timerRef = useRef(null);
    const [formFieldSupported, setFormFieldSupported] = useState({});
    const [showEmojiPicker, setShowEmojiPicker] = useState(false);
    const emojiPickerRef = useRef(null);

    useEffect(() => {
        const settings = window.supported_settings; // Assuming it's globally defined
        setFormFieldSupported(settings);
    }, []);

    useEffect(() => {
        function handleClickOutside(event) {
            if (emojiPickerRef.current && !emojiPickerRef.current.contains(event.target)) {
                setShowEmojiPicker(false);
            }
        }

        document.addEventListener("mousedown", handleClickOutside);
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, []);

    const startRecording = async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorderRef.current = new MediaRecorder(stream);
            audioChunksRef.current = [];

            mediaRecorderRef.current.ondataavailable = (e) => {
                if (e.data.size > 0) {
                    audioChunksRef.current.push(e.data);
                }
            };

            mediaRecorderRef.current.onstop = () => {
                const audioBlob = new Blob(audioChunksRef.current, { type: 'audio/wav' });
                setAudioBlob(audioBlob);
                stream.getTracks().forEach(track => track.stop());
                if (timerRef.current) {
                    clearInterval(timerRef.current);
                    timerRef.current = null;
                }
            };

            mediaRecorderRef.current.start();
            setIsRecording(true);
            setRecordingTime(0);
            timerRef.current = setInterval(() => {
                setRecordingTime(prev => prev + 1);
            }, 1000);
        } catch (error) {
            console.error("Error accessing microphone:", error);
            alert("Could not access microphone. Please check your permissions.");
        }
    };

    const stopRecording = () => {
        if (mediaRecorderRef.current && mediaRecorderRef.current.state !== "inactive") {
            mediaRecorderRef.current.stop();
            setIsRecording(false);
        }
    };

    const handleFormSubmit = (e) => {
        e.preventDefault();
        if ((!input.trim() && !image && !audioBlob) || isRecording) return;
        setShowEmojiPicker(false);
        handleSubmit(e, audioBlob);
        setAudioBlob(null);
    };

    const formatTime = (seconds) => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    const addEmoji = (emoji) => {
        setInput(prev => prev + emoji);
    };

    return (
        <div className="flex flex-col p-3 border-t border-gray-200 bg-white">
            {(image || audioBlob) && (
                <div className="flex items-center gap-3 mb-3 px-3 py-2 bg-gray-50 rounded-lg">
                    {image && (
                        <div className="relative group">
                            <img
                                src={URL.createObjectURL(image)}
                                alt={image.name}
                                className="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-200"
                            />
                            <button
                                onClick={() => setImage(null)}
                                className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                ×
                            </button>
                        </div>
                    )}
                    {audioBlob && (
                        <div className="flex-1 flex items-center gap-2 relative group">
                            <audio
                                src={URL.createObjectURL(audioBlob)}
                                controls
                                className="h-8 w-full max-w-xs rounded"
                            />
                            <button
                                onClick={() => setAudioBlob(null)}
                                className="absolute -top-2 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                ×
                            </button>
                        </div>
                    )}
                </div>
            )}

            {isRecording && (
                <div className="flex items-center gap-2 bg-red-50 text-red-600 py-2 px-4 rounded-full mb-3 animate-pulse">
                    <div className="w-2 h-2 bg-red-600 rounded-full"></div>
                    <span className="text-sm font-medium">Recording... {formatTime(recordingTime)}</span>
                </div>
            )}

            <form className="flex items-center gap-2" onSubmit={handleFormSubmit}>
                <div className="flex items-center bg-gray-100 rounded-full flex-grow px-3 py-1 border border-transparent focus-within:border-gray-300 transition-all relative">

                    {formFieldSupported?.active_voice === '1' && (
                        <button
                            type="button"
                            onClick={isRecording ? stopRecording : startRecording}
                            className={`${isRecording ? 'text-red-500' : 'text-gray-500'} p-2 hover:bg-gray-200 rounded-full transition-all`}
                        >
                            {isRecording ? <FaStop size={16} /> : <BsFillMicFill size={18} />}
                        </button>
                    )}

                    <input
                        className="flex-grow bg-transparent p-2 outline-none placeholder-gray-500 text-gray-800 focus:border-none w-full text-base"
                        placeholder="Type a message..."
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        disabled={isRecording}
                    />

                    {formFieldSupported?.active_image === '1' && (
                        <button
                            type="button"
                            onClick={() => fileInputRef.current.click()}
                            className="text-gray-500 p-2 hover:bg-gray-200 rounded-full transition-all"
                            disabled={isRecording}
                        >
                            <IoMdImage size={20} />
                        </button>
                    )}

                    {formFieldSupported?.active_emoji === '1' && (
                        <div className="relative">
                            <button
                                type="button"
                                className={`text-gray-500 p-2 hover:bg-gray-200 rounded-full transition-all ${showEmojiPicker ? 'bg-gray-200' : ''}`}
                                disabled={isRecording}
                                onClick={() => setShowEmojiPicker(!showEmojiPicker)}
                            >
                                <FaRegSmile size={18} />
                            </button>

                            {showEmojiPicker && (
                                <div
                                    ref={emojiPickerRef}
                                    className="absolute bottom-12 right-0 bg-white border border-gray-200 rounded-lg shadow-lg p-2 z-10 w-64"
                                >
                                    <div className="grid grid-cols-8 gap-1">
                                        {emojiList.map((emoji, index) => (
                                            <button
                                                key={index}
                                                type="button"
                                                className="hover:bg-gray-100 p-1 rounded transition-colors w-8 h-8 flex items-center justify-center text-lg"
                                                onClick={() => addEmoji(emoji)}
                                            >
                                                {emoji}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    )}
                </div>

                <input
                    id="file_input"
                    type="file"
                    accept="image/*"
                    onChange={handleImage}
                    ref={fileInputRef}
                    className="hidden"
                />

                <button
                    type="submit"
                    className={`w-10 h-10 flex items-center justify-center rounded-full transition-all hidden ${
                        (!input.trim() && !image && !audioBlob) || isRecording
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-blue-600 text-white shadow-md hover:bg-blue-700'
                    }`}
                    disabled={(!input.trim() && !image && !audioBlob) || isRecording}
                >
                    <IoIosSend size={18} />
                </button>
            </form>
        </div>
    );
};

export default ChatboxForm;
