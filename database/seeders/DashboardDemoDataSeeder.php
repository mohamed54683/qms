<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreVisit;
use App\Models\ActionPlan;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;

class DashboardDemoDataSeeder extends Seeder
{
    public function run()
    {
        echo "🌱 جاري إضافة بيانات تجريبية للوحة القيادة...\n\n";
        
        // Get restaurants and user
        $restaurants = Restaurant::where('is_active', true)->take(20)->get();
        $user = User::first();
        
        if (!$user) {
            echo "❌ لا يوجد مستخدمين في النظام\n";
            return;
        }
        
        if ($restaurants->isEmpty()) {
            echo "❌ لا توجد مطاعم في النظام\n";
            return;
        }
        
        echo "📊 إنشاء زيارات المتاجر...\n";
        
        $statuses = ['Draft', 'Submitted', 'Completed', 'Approved', 'Rejected'];
        $purposes = ['QSC Inspection', 'Follow Up Visit', 'Routine Check', 'Compliance Audit'];
        $visits = [];
        
        // Create visits for the last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30));
            $restaurant = $restaurants->random();
            
            $score = rand(70, 100);
            $status = $statuses[array_rand($statuses)];
            
            $visit = StoreVisit::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'visit_date' => $date,
                'purpose_of_visit' => $purposes[array_rand($purposes)],
                'status' => $status,
                'score' => $score,
                'general_comments' => $this->getRandomNote(),
                'completed_at' => in_array($status, ['Completed', 'Approved']) ? $date->addHours(2) : null,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            $visits[] = $visit;
            echo "  ✓ زيارة {$restaurant->name} - النتيجة: {$score}%\n";
        }
        
        echo "\n📋 إنشاء خطط العمل...\n";
        
        $priorities = ['High', 'Medium', 'Low'];
        $planStatuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];
        $items = [
            'تحسين نظافة المطبخ',
            'صيانة معدات التبريد',
            'تدريب الموظفين على معايير الجودة',
            'إصلاح نظام التكييف',
            'تحديث قائمة الطعام',
            'تحسين خدمة العملاء',
            'فحص صلاحية المواد الغذائية',
            'ترتيب منطقة التخزين',
            'تنظيف وتعقيم الطاولات',
            'استبدال معدات المطبخ التالفة',
            'تحسين إجراءات السلامة الغذائية',
            'تدريب على إدارة النفايات',
            'فحص أنظمة الإطفاء',
            'تحديث لوائح الصحة والسلامة',
            'تحسين مظهر المطعم الخارجي',
        ];
        
        foreach ($visits as $visit) {
            // Create 0-2 action plans per visit
            $numPlans = rand(0, 2);
            
            for ($j = 0; $j < $numPlans; $j++) {
                $status = $planStatuses[array_rand($planStatuses)];
                $priority = $priorities[array_rand($priorities)];
                $dueDate = Carbon::now()->addDays(rand(1, 30));
                
                if ($status === 'Completed') {
                    $dueDate = Carbon::now()->subDays(rand(1, 10));
                }
                
                ActionPlan::create([
                    'store_visit_id' => $visit->id,
                    'item' => $items[array_rand($items)],
                    'issue' => $this->getRandomIssue(),
                    'priority' => $priority,
                    'status' => $status,
                    'due_date' => $dueDate,
                    'assigned_to' => $user->id,
                    'what' => 'تحديد المشكلة ووضع خطة للحل',
                    'where' => $visit->restaurant_name,
                    'why' => 'لتحسين الجودة والالتزام بالمعايير',
                    'how' => 'من خلال التدريب والمتابعة المستمرة',
                    'who' => $user->name,
                    'when_detail' => $dueDate->format('Y-m-d'),
                    'action_required' => 'متابعة وتنفيذ الإجراءات التصحيحية',
                    'created_at' => $visit->created_at,
                    'updated_at' => $visit->created_at,
                ]);
                
                echo "  ✓ خطة عمل: {$items[array_rand($items)]} - {$status}\n";
            }
        }
        
        echo "\n✅ تم إضافة البيانات التجريبية بنجاح!\n";
        echo "📊 الإحصائيات:\n";
        echo "   - عدد الزيارات: " . count($visits) . "\n";
        echo "   - عدد خطط العمل: " . ActionPlan::count() . "\n";
    }
    
    private function getRandomNote()
    {
        $notes = [
            'زيارة روتينية - جميع الأمور على ما يرام',
            'تم العثور على بعض المشاكل البسيطة تحتاج متابعة',
            'أداء ممتاز - التزام كامل بالمعايير',
            'يحتاج تحسين في بعض المجالات',
            'تم إصدار تحذير للمدير بشأن النظافة',
            'زيارة متابعة للتأكد من تنفيذ الإجراءات التصحيحية',
        ];
        
        return $notes[array_rand($notes)];
    }
    
    private function getRandomIssue()
    {
        $issues = [
            'عدم الالتزام بمعايير النظافة',
            'معدات بحاجة للصيانة',
            'نقص في التدريب',
            'عدم اتباع إجراءات السلامة الغذائية',
            'تخزين غير صحيح للمواد الغذائية',
            'عدم توفر معدات الحماية الشخصية',
            'ضعف في خدمة العملاء',
        ];
        
        return $issues[array_rand($issues)];
    }
}
