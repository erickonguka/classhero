<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::where('role', 'teacher')->get();
        $categories = Category::all();

        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'short_description' => 'Learn HTML, CSS, JavaScript, React, Node.js and more',
                'description' => 'This comprehensive course covers everything you need to know to become a full-stack web developer. Starting from the basics of HTML and CSS, you\'ll progress through JavaScript, React, Node.js, and database management.',
                'category' => 'Web Development',
                'difficulty' => 'beginner',
                'is_free' => true,
                'duration_hours' => 40,
                'what_you_learn' => [
                    'HTML5 and CSS3 fundamentals',
                    'JavaScript ES6+ features',
                    'React.js for frontend development',
                    'Node.js and Express.js for backend',
                    'Database design and management'
                ],
                'requirements' => [
                    'Basic computer skills',
                    'No programming experience required'
                ],
                'lessons' => [
                    ['title' => 'Introduction to HTML', 'type' => 'video', 'duration' => 30, 'video_url' => 'https://www.youtube.com/embed/UB1O30fR-EE'],
                    ['title' => 'CSS Styling Basics', 'type' => 'video', 'duration' => 45, 'video_url' => 'https://www.youtube.com/embed/yfoY53QXEnI'],
                    ['title' => 'JavaScript Fundamentals', 'type' => 'video', 'duration' => 60, 'video_url' => 'https://www.youtube.com/embed/PkZNo7MFNFg'],
                    ['title' => 'Building Your First Website', 'type' => 'text', 'duration' => 90],
                ]
            ],
            [
                'title' => 'UI/UX Design Masterclass',
                'short_description' => 'Master the art of user interface and user experience design',
                'description' => 'Learn the principles of great design, user research, wireframing, prototyping, and creating beautiful user interfaces that provide excellent user experiences.',
                'category' => 'Design',
                'difficulty' => 'intermediate',
                'is_free' => false,
                'price' => 99.99,
                'duration_hours' => 25,
                'what_you_learn' => [
                    'Design thinking principles',
                    'User research methods',
                    'Wireframing and prototyping',
                    'Visual design principles',
                    'Usability testing'
                ],
                'requirements' => [
                    'Basic design knowledge helpful',
                    'Access to design software (Figma recommended)'
                ],
                'lessons' => [
                    ['title' => 'Design Thinking Process', 'type' => 'video', 'duration' => 40, 'video_url' => 'https://www.youtube.com/embed/_r0VX-aU_T8'],
                    ['title' => 'User Research Methods', 'type' => 'text', 'duration' => 35],
                    ['title' => 'Creating Wireframes', 'type' => 'video', 'duration' => 50, 'video_url' => 'https://www.youtube.com/embed/qpH7-KFWZRI'],
                ]
            ],
            [
                'title' => 'Data Science with Python',
                'short_description' => 'Learn data analysis, visualization, and machine learning',
                'description' => 'Dive into the world of data science using Python. Learn to analyze data, create visualizations, and build machine learning models to solve real-world problems.',
                'category' => 'Data Science',
                'difficulty' => 'intermediate',
                'is_free' => true,
                'duration_hours' => 35,
                'what_you_learn' => [
                    'Python programming for data science',
                    'Data manipulation with Pandas',
                    'Data visualization with Matplotlib',
                    'Machine learning algorithms',
                    'Statistical analysis'
                ],
                'requirements' => [
                    'Basic Python knowledge',
                    'High school mathematics'
                ],
                'lessons' => [
                    ['title' => 'Python for Data Science', 'type' => 'video', 'duration' => 45, 'video_url' => 'https://www.youtube.com/embed/LHBE6Q9XlzI'],
                    ['title' => 'Working with Pandas', 'type' => 'video', 'duration' => 60, 'video_url' => 'https://www.youtube.com/embed/vmEHCJofslg'],
                    ['title' => 'Data Visualization', 'type' => 'text', 'duration' => 40],
                ]
            ],
            [
                'title' => 'iOS App Development with Swift',
                'short_description' => 'Build native iOS applications using Swift and Xcode',
                'description' => 'Learn to create beautiful and functional iOS applications using Swift programming language and Xcode development environment.',
                'category' => 'Mobile Development',
                'difficulty' => 'advanced',
                'is_free' => false,
                'price' => 149.99,
                'duration_hours' => 50,
                'what_you_learn' => [
                    'Swift programming language',
                    'iOS app architecture',
                    'User interface design',
                    'Core Data for data persistence',
                    'App Store submission process'
                ],
                'requirements' => [
                    'Mac computer with Xcode',
                    'Basic programming knowledge',
                    'Apple Developer account (for testing)'
                ],
                'lessons' => [
                    ['title' => 'Swift Basics', 'type' => 'video', 'duration' => 55, 'video_url' => 'https://www.youtube.com/embed/comQ1-x2a1Q'],
                    ['title' => 'Building Your First App', 'type' => 'video', 'duration' => 70, 'video_url' => 'https://www.youtube.com/embed/09TeUXjzpKs'],
                    ['title' => 'Working with APIs', 'type' => 'text', 'duration' => 45],
                ]
            ]
        ];

        foreach ($courses as $courseData) {
            $category = $categories->where('name', $courseData['category'])->first();
            $teacher = $teachers->random();
            
            $course = Course::create([
                'title' => $courseData['title'],
                'slug' => Str::slug($courseData['title']),
                'short_description' => $courseData['short_description'],
                'description' => $courseData['description'],
                'teacher_id' => $teacher->id,
                'category_id' => $category->id,
                'difficulty' => $courseData['difficulty'],
                'status' => 'published',
                'is_free' => $courseData['is_free'],
                'price' => $courseData['price'] ?? null,
                'has_certificate' => true,
                'duration_hours' => $courseData['duration_hours'],
                'total_lessons' => count($courseData['lessons']),
                'enrolled_count' => rand(50, 500),
                'rating' => rand(40, 50) / 10,
                'rating_count' => rand(10, 100),
                'what_you_learn' => $courseData['what_you_learn'],
                'requirements' => $courseData['requirements'],
            ]);

            // Create lessons for each course
            foreach ($courseData['lessons'] as $index => $lessonData) {
                $lesson = Lesson::create([
                    'title' => $lessonData['title'],
                    'slug' => Str::slug($lessonData['title']),
                    'course_id' => $course->id,
                    'content' => 'This is the content for ' . $lessonData['title'] . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'type' => $lessonData['type'],
                    'video_url' => $lessonData['video_url'] ?? null,
                    'duration_minutes' => $lessonData['duration'],
                    'order' => $index + 1,
                    'is_free' => $index === 0, // First lesson is always free
                    'is_published' => true,
                ]);

                // Create quiz for each lesson
                $quiz = Quiz::create([
                    'title' => $lessonData['title'] . ' Quiz',
                    'description' => 'Test your knowledge of ' . $lessonData['title'],
                    'lesson_id' => $lesson->id,
                    'passing_score' => 70,
                    'max_attempts' => 3,
                    'time_limit' => 10, // 10 minutes
                    'is_required' => true,
                    'show_results' => true,
                ]);

                // Create sample questions
                $questions = [
                    [
                        'question' => 'What is the main topic of this lesson?',
                        'type' => 'multiple_choice',
                        'options' => [$lessonData['title'], 'Something else', 'Not sure', 'All of the above'],
                        'correct_answers' => [0],
                        'explanation' => 'The lesson focuses on ' . $lessonData['title'],
                        'points' => 10
                    ],
                    [
                        'question' => 'This lesson is useful for beginners.',
                        'type' => 'true_false',
                        'correct_answers' => ['true'],
                        'explanation' => 'Yes, this lesson is designed for beginners.',
                        'points' => 5
                    ]
                ];

                foreach ($questions as $qIndex => $questionData) {
                    QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question' => $questionData['question'],
                        'type' => $questionData['type'],
                        'options' => $questionData['options'] ?? null,
                        'correct_answers' => $questionData['correct_answers'],
                        'explanation' => $questionData['explanation'],
                        'points' => $questionData['points'],
                        'order' => $qIndex + 1,
                    ]);
                }
            }
        }
    }
}