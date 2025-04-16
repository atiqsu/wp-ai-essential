import { useEffect, useRef } from 'react';
import { FaPlay } from 'react-icons/fa';

const ChatHistory = ({ messages, loading }) => {
    const bottomRef = useRef(null);

    useEffect(() => {
        if (bottomRef.current) {
            bottomRef.current.scrollIntoView({ behavior: 'smooth' });
        }
    }, [messages, loading]);

    const formatMessageTime = (date) => {
        // Using native JS date formatting instead of date-fns
        const dateObj = new Date(date);
        let hours = dateObj.getHours();
        const minutes = dateObj.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // Convert 0 to 12
        return `${hours}:${minutes} ${ampm}`;
    };

    return (
        <div className="p-4 space-y-4 h-full overflow-y-auto bg-gray-50">
            {messages.map((msg, index) => {
                const isUser = msg.type === 'user';
                const showAvatar = !isUser;

                return (
                    <div key={index} className={`flex ${isUser ? 'justify-end' : 'justify-start'}`}>
                        <div className="flex items-end max-w-[80%] gap-2">
                            {showAvatar && (
                                <div className="w-8 h-8 rounded-full bg-black flex items-center justify-center text-white flex-shrink-0 mb-1">
                                    AI
                                </div>
                            )}

                            <div className={`group flex flex-col space-y-1 ${isUser ? 'items-end' : 'items-start'}`}>
                                <div
                                    className={`px-4 py-3 rounded-2xl shadow-sm ${
                                        isUser
                                            ? 'bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-br-none'
                                            : 'bg-white text-gray-800 rounded-bl-none border border-gray-100'
                                    }`}
                                >
                                    {msg.text && <p className="text-sm max-w-[200px] w-full">{msg.text}</p>}

                                    {msg.file && (
                                        <div className="mt-2 relative">
                                            <img
                                                src={URL.createObjectURL(msg.file)}
                                                alt="Uploaded"
                                                className="rounded-lg max-w-full object-contain max-h-40"
                                            />
                                        </div>
                                    )}

                                    {msg.audioFile && (
                                        <div className={`mt-2 flex items-center gap-2 ${isUser ? 'text-blue-100' : 'text-gray-600'}`}>
                                            <div className="w-full">
                                                <audio
                                                    src={URL.createObjectURL(msg.audioFile)}
                                                    controls
                                                    className={`max-w-full h-8 ${isUser ? 'audio-player-light' : 'audio-player-dark'}`}
                                                />
                                            </div>
                                        </div>
                                    )}
                                </div>

                                <span
                                    className={`text-xs text-gray-500 px-2 opacity-0 group-hover:opacity-100 transition-opacity`}
                                >
                                    {formatMessageTime(msg.time)}
                                </span>
                            </div>

                            {isUser && (
                                <div className="w-8 h-8 rounded-full bg-gray-200 flex text-base items-center justify-center text-gray-500 flex-shrink-0 mb-1">
                                    U
                                </div>
                            )}
                        </div>
                    </div>
                );
            })}

            {loading && (
                <div className="flex justify-start">
                    <div className="flex items-end gap-2">
                        <div className="w-8 h-8 rounded-full bg-black flex items-center justify-center text-white flex-shrink-0">
                            AI
                        </div>
                        <div className="px-4 py-3 rounded-2xl bg-white text-gray-500 shadow-sm max-w-[60%] rounded-bl-none border border-gray-100">
                            <div className="flex space-x-1">
                                <div className="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style={{ animationDelay: '0ms' }}></div>
                                <div className="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style={{ animationDelay: '200ms' }}></div>
                                <div className="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style={{ animationDelay: '400ms' }}></div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <div ref={bottomRef} />
        </div>
    );
};

export default ChatHistory;