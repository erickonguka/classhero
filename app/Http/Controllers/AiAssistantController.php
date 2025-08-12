<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    public function chat(Request $request, Lesson $lesson)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $apiKey = env('GEMINI_API_KEY');
        \Log::info('AI Chat Debug - API Key exists: ' . ($apiKey ? 'Yes' : 'No'));
        
        if (!$apiKey) {
            \Log::error('AI Chat Error: No API key configured');
            return response()->json(['error' => 'AI service not configured'], 500);
        }

        // Prepare lesson context
        $context = $this->prepareLessonContext($lesson);
        \Log::info('AI Chat Debug - Context prepared for lesson: ' . $lesson->id);
        
        $prompt = "You are an AI tutor helping students understand course materials. Based on the lesson content provided below, answer the student's question strictly according to the lesson material. If the question is not related to the lesson content, politely redirect them to focus on the lesson.\n\nLesson Context:\n{$context}\n\nStudent Question: {$request->message}\n\nProvide a helpful, educational response based only on the lesson content:";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];
        
        \Log::info('AI Chat Debug - Making request to: ' . $url);
        \Log::info('AI Chat Debug - Payload: ' . json_encode($payload));

        try {
            $response = Http::timeout(30)->post($url, $payload);
            
            \Log::info('AI Chat Debug - Response status: ' . $response->status());
            \Log::info('AI Chat Debug - Response body: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';
                
                \Log::info('AI Chat Success - Response generated');
                return response()->json([
                    'success' => true,
                    'response' => $aiResponse
                ]);
            }

            \Log::error('AI Chat Error - API request failed with status: ' . $response->status());
            return response()->json(['error' => 'AI service unavailable'], 500);
        } catch (\Exception $e) {
            \Log::error('AI Chat Exception: ' . $e->getMessage());
            \Log::error('AI Chat Exception Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to get AI response'], 500);
        }
    }

    private function prepareLessonContext(Lesson $lesson)
    {
        $context = "Lesson Title: {$lesson->title}\n";
        $context .= "Course: {$lesson->course->title}\n";
        $context .= "Lesson Type: {$lesson->type}\n\n";
        
        if ($lesson->content) {
            $context .= "Lesson Content:\n" . strip_tags($lesson->content) . "\n\n";
        }

        // Add media descriptions
        if ($lesson->lessonMedia->count() > 0) {
            $context .= "Lesson Materials:\n";
            foreach ($lesson->lessonMedia as $media) {
                $context .= "- {$media->type}";
                if ($media->title) {
                    $context .= ": {$media->title}";
                }
                if ($media->description) {
                    $context .= " - {$media->description}";
                }
                $context .= "\n";
            }
        }

        return $context;
    }
}