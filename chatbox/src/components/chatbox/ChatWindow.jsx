import React, { useEffect, useState, useRef } from 'react';
import { IoMdClose } from 'react-icons/io';
import { IoIosArrowDown } from 'react-icons/io';
import ChatHistory from './ChatHistory.jsx';
import ChatboxForm from "./ChatboxForm.jsx";

const ChatWindow = () => {
    const [input, setInput] = useState('');
    const [messages, setMessages] = useState([]);
    const [loading, setLoading] = useState(false);
    const [reply, setReply] = useState('');
    const [image, setImage] = useState(null);
    const [isMinimized, setIsMinimized] = useState(false);
    const fileInputRef = useRef(null);

    // Fetch the initial reply from the backend
    useEffect(() => {
        fetch('http://development.local/wp-json/wai/v2/agent-chat')
            .then(res => res.json())
            .then(json => setReply(json))
            .catch(err => console.error("Error fetching data:", err));
    }, []);

    // Set the initial message only once
    useEffect(() => {
        const firstMessage = {
            text: 'Hello! I am your virtual assistant. How can I help you today?',
            type: 'admin',
            time: new Date(),
            file: '',
            audioFile: null
        };
        setMessages([firstMessage]);
    }, []);

    const uploadMediaToWordPress = async (file) => {
        const formData = new FormData();
        formData.append("file", file);
        try {
            const res = await fetch("http://development.local/wp-json/wai/v2/upload-media", {
                method: "POST",
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                // console.log("✅ Image uploaded:", data.url);
                return data.url;
            } else {
                console.error("❌ Upload error:", data.error);
                return null;
            }
        } catch (error) {
            console.error("❌ Fetch error:", error);
            return null;
        }
    };

    const handleSubmit = async (e, audioBlob = null) => {
        e.preventDefault();
        if (!input.trim() && !image && !audioBlob) return;

        let uploadedImageUrl = null;

        if (image) {
            uploadedImageUrl = await uploadMediaToWordPress(image);
        }



        // console.log("audio file is :", audioBlob);

        const newMessages = [...messages, {
            text: input,
            type: 'user',
            time: new Date(),
            file: uploadedImageUrl ? uploadedImageUrl : image,
            audioFile: audioBlob
        }];

        setMessages(newMessages);
        setInput('');
        setImage(null);
        setLoading(true);

        if (fileInputRef.current) {
            fileInputRef.current.value = null;
        }

        setTimeout(() => {
            setMessages([...newMessages, {
                text: reply,
                type: 'admin',
                time: new Date(),
                file: '',
                audioFile: null
            }]);
            setLoading(false);
        }, 2000);
    };

    const handleImage = (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"];
        const maxSize = 2 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            alert("Only JPG, JPEG, PNG, and WEBP files are allowed.");
            return;
        }

        if (file.size > maxSize) {
            alert("File size must be 2MB or less.");
            return;
        }

        setImage(file);
    };

    console.log(messages)
    return (
        <div>
            <div
                className={`rounded-3xl shadow-2xl border border-gray-200 fixed ${
                    isMinimized ? 'h-16 bottom-20' : 'h-[500px]'
                } right-4 bottom-20 w-[350px] overflow-hidden flex flex-col justify-between transition-all duration-300 ease-in-out`}
                style={{ boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)' }}
            >
                {/* Header */}
                <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-3 px-4 flex items-center justify-between z-[99]">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <span className="text-xl font-bold">AI</span>
                        </div>
                        <div>
                            <h1 className="font-semibold text-white text-base">AI Assistant</h1>
                            <div className="text-xs flex items-center">
                                <span className="w-2 h-2 bg-green-400 rounded-full inline-block mr-2"></span>
                                Online
                            </div>
                        </div>
                    </div>
                    <div className="flex gap-1">
                        <button
                            onClick={() => setIsMinimized(!isMinimized)}
                            className="p-2 hover:bg-white/10 rounded-full transition-colors"
                        >
                            <IoIosArrowDown size={18} className={`transform transition-transform ${isMinimized ? 'rotate-180' : ''}`} />
                        </button>
                        {/*<button onClick={showChatWindow} className="p-2 hover:bg-white/10 rounded-full transition-colors">*/}
                        {/*    <IoMdClose size={18} />*/}
                        {/*</button>*/}
                    </div>
                </div>

                {/* Chat body - only visible when not minimized */}
                {!isMinimized && (
                    <>
                        <div className="flex-1 overflow-y-auto message-body">
                            <ChatHistory messages={messages} loading={loading}/>
                        </div>
                        <div>
                            <ChatboxForm
                                input={input}
                                setInput={setInput}
                                handleSubmit={handleSubmit}
                                handleImage={handleImage}
                                image={image}
                                fileInputRef={fileInputRef}
                                setImage={setImage}
                            />
                        </div>
                    </>
                )}
            </div>

            {/* Add some global styles for audio players */}
            <style jsx global>{`
                    .audio-player-light::-webkit-media-controls-panel {
                        background-color: rgba(255, 255, 255, 0.2);
                    }
                    .audio-player-light::-webkit-media-controls-current-time-display,
                    .audio-player-light::-webkit-media-controls-time-remaining-display {
                        color: white;
                    }
                    .audio-player-dark::-webkit-media-controls-panel {
                        background-color: rgba(240, 240, 240, 0.8);
                    }
                `}</style>
        </div>
    );
};

export default ChatWindow;