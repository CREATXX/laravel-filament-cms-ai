<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Google\Analytics\Data\V1beta\RunReportRequest;

class AnalyticsService
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        // Service Account credentials kontrolü
        if (!file_exists(config('analytics.service_account_credentials_json'))) {
            throw new \Exception('Google Analytics service account credentials file not found');
        }

        // Google Analytics Data API client
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . config('analytics.service_account_credentials_json'));
        
        $this->client = new BetaAnalyticsDataClient();
        $this->propertyId = 'properties/' . (config('analytics.property_id') ?: Setting::get('google_analytics_property_id', ''));
    }

    /**
     * Visitor istatistikleri - Son 30 gün
     */
    public function getVisitorStats(int $days = 30)
    {
        $cacheKey = "analytics_visitor_stats_{$days}";
        
        return Cache::remember($cacheKey, config('analytics.cache_lifetime_in_minutes') * 60, function () use ($days) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => "{$days}daysAgo",
                            'end_date' => 'today',
                        ])
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'averageSessionDuration']),
                        new Metric(['name' => 'bounceRate']),
                    ]);

                $response = $this->client->runReport($request);

                if ($response->getRowCount() === 0) {
                    return [
                        'activeUsers' => 0,
                        'sessions' => 0,
                        'pageViews' => 0,
                        'avgSessionDuration' => 0,
                        'bounceRate' => 0,
                    ];
                }

                $row = $response->getRows()[0];
                $metricValues = $row->getMetricValues();

                return [
                    'activeUsers' => (int) $metricValues[0]->getValue(),
                    'sessions' => (int) $metricValues[1]->getValue(),
                    'pageViews' => (int) $metricValues[2]->getValue(),
                    'avgSessionDuration' => round((float) $metricValues[3]->getValue(), 2),
                    'bounceRate' => round((float) $metricValues[4]->getValue() * 100, 2),
                ];
            } catch (\Exception $e) {
                \Log::error('Analytics API Error: ' . $e->getMessage());
                return [
                    'activeUsers' => 0,
                    'sessions' => 0,
                    'pageViews' => 0,
                    'avgSessionDuration' => 0,
                    'bounceRate' => 0,
                ];
            }
        });
    }

    /**
     * Günlük visitor chart verisi
     */
    public function getVisitorChart(int $days = 30)
    {
        $cacheKey = "analytics_visitor_chart_{$days}";
        
        return Cache::remember($cacheKey, config('analytics.cache_lifetime_in_minutes') * 60, function () use ($days) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => "{$days}daysAgo",
                            'end_date' => 'today',
                        ])
                    ])
                    ->setDimensions([new Dimension(['name' => 'date'])])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'screenPageViews']),
                    ]);

                $response = $this->client->runReport($request);

                $chartData = [];
                foreach ($response->getRows() as $row) {
                    $date = $row->getDimensionValues()[0]->getValue();
                    $formattedDate = \Carbon\Carbon::createFromFormat('Ymd', $date)->format('d.m.Y');
                    
                    $chartData[] = [
                        'date' => $formattedDate,
                        'visitors' => (int) $row->getMetricValues()[0]->getValue(),
                        'pageViews' => (int) $row->getMetricValues()[1]->getValue(),
                    ];
                }

                return $chartData;
            } catch (\Exception $e) {
                \Log::error('Analytics Chart API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * En çok ziyaret edilen sayfalar
     */
    public function getTopPages(int $limit = 10)
    {
        $cacheKey = "analytics_top_pages_{$limit}";
        
        return Cache::remember($cacheKey, config('analytics.cache_lifetime_in_minutes') * 60, function () use ($limit) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ])
                    ])
                    ->setDimensions([
                        new Dimension(['name' => 'pageTitle']),
                        new Dimension(['name' => 'pagePath']),
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'averageSessionDuration']),
                    ])
                    ->setLimit($limit);

                $response = $this->client->runReport($request);

                $pages = [];
                foreach ($response->getRows() as $row) {
                    $dimensions = $row->getDimensionValues();
                    $metrics = $row->getMetricValues();
                    
                    $pages[] = [
                        'title' => $dimensions[0]->getValue(),
                        'path' => $dimensions[1]->getValue(),
                        'pageViews' => (int) $metrics[0]->getValue(),
                        'avgDuration' => round((float) $metrics[1]->getValue(), 2),
                    ];
                }

                return $pages;
            } catch (\Exception $e) {
                \Log::error('Analytics Top Pages API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Gerçek zamanlı ziyaretçiler
     */
    public function getRealtimeUsers()
    {
        try {
            $request = (new RunRealtimeReportRequest())
                ->setProperty($this->propertyId)
                ->setMetrics([
                    new Metric(['name' => 'activeUsers']),
                ]);

            $response = $this->client->runRealtimeReport($request);

            if ($response->getRowCount() === 0) {
                return 0;
            }

            return (int) $response->getRows()[0]->getMetricValues()[0]->getValue();
        } catch (\Exception $e) {
            \Log::error('Analytics Realtime API Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Traffic sources (kaynaklar)
     */
    public function getTrafficSources(int $limit = 5)
    {
        $cacheKey = "analytics_traffic_sources_{$limit}";
        
        return Cache::remember($cacheKey, config('analytics.cache_lifetime_in_minutes') * 60, function () use ($limit) {
            try {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ])
                    ])
                    ->setDimensions([
                        new Dimension(['name' => 'sessionSource']),
                        new Dimension(['name' => 'sessionMedium']),
                    ])
                    ->setMetrics([
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'activeUsers']),
                    ])
                    ->setLimit($limit);

                $response = $this->client->runReport($request);

                $sources = [];
                foreach ($response->getRows() as $row) {
                    $dimensions = $row->getDimensionValues();
                    $metrics = $row->getMetricValues();
                    
                    $sources[] = [
                        'source' => $dimensions[0]->getValue(),
                        'medium' => $dimensions[1]->getValue(),
                        'sessions' => (int) $metrics[0]->getValue(),
                        'users' => (int) $metrics[1]->getValue(),
                    ];
                }

                return $sources;
            } catch (\Exception $e) {
                \Log::error('Analytics Traffic Sources API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Cihaz dağılımı (Desktop, Mobile, Tablet)
     */
    public function getDeviceStats()
    {
        $cacheKey = "analytics_device_stats";
        
        return Cache::remember($cacheKey, config('analytics.cache_lifetime_in_minutes') * 60, function () {
            try {
                $request = (new RunReportRequest())
                    ->setProperty($this->propertyId)
                    ->setDateRanges([
                        new DateRange([
                            'start_date' => '30daysAgo',
                            'end_date' => 'today',
                        ])
                    ])
                    ->setDimensions([new Dimension(['name' => 'deviceCategory'])])
                    ->setMetrics([
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'sessions']),
                    ]);

                $response = $this->client->runReport($request);

                $devices = [];
                foreach ($response->getRows() as $row) {
                    $device = $row->getDimensionValues()[0]->getValue();
                    $metrics = $row->getMetricValues();
                    
                    $devices[$device] = [
                        'users' => (int) $metrics[0]->getValue(),
                        'sessions' => (int) $metrics[1]->getValue(),
                    ];
                }

                return $devices;
            } catch (\Exception $e) {
                \Log::error('Analytics Device Stats API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Cache temizleme
     */
    public function clearCache()
    {
        Cache::flush();
    }
}
