# PRODUCTION OPTIMIZATION PLAN
**Project:** PEMIRA WEB (Sistem Pemilihan Raya Mahasiswa)  
**Execution Strategy:** Minimal Change, Maximum Reliability  
**Target:** 100% Zero-Regression, High-Concurrency Production Readiness

---

## 1. Prioritization Matrix

```
[HIGH] PERF-01: Eliminate N+1 Query in DashboardDataProvider
[HIGH] PERF-02: Eliminate N+1 Query in ElectionResultService
[HIGH] PERF-04: Cache SystemSetting isMaintenanceMode()
       │
[MEDIUM] PERF-03: In-Request Memoization for Election::current()
[MEDIUM] DB-01: Composite Covering Index on eligible_voters (study_program_id, is_eligible)
[MEDIUM] FE-01: Eliminate CSS Render-Blocking Google Font @import
       │
[LOW] PERF-05: Eliminate Duplicate Query in SpecialActions
[LOW] DB-02: Index on schedule_change_requests (election_id, status)
       │
[CLEANUP] CLEAN-01: Remove Verified Unused Components (resources/views/components/public/)
```

---

## 2. Implementation Summary

### Phase 1: High Priority (High Throughput & Database Offloading)
1. **[PERF-01]** Ganti loop individual dengan agregasi SQL `GROUP BY` di `DashboardDataProvider`.
2. **[PERF-02]** Terapkan agregasi `GROUP BY` yang sama di `ElectionResultService`.
3. **[PERF-04]** Tambahkan `Cache::rememberForever` dan `Cache::forget` pada `SystemSetting`.

### Phase 2: Medium Priority (Query Memoization & Frontend FCP)
1. **[PERF-03]** Terapkan in-request memoization (`request()->attributes`) pada `Election::current()`.
2. **[DB-01 & DB-02]** Buat migration index komposit `eligible_voters` dan `schedule_change_requests`.
3. **[FE-01]** Pindahkan Google Fonts dari CSS `@import` ke HTML `<link rel="stylesheet">` non-blocking.

### Phase 3: Low Priority (Refinements)
1. **[PERF-05]** Gunakan `$this->recentMaintenanceLogs->first()` untuk properti `lastMaintenanceLog`.

### Phase 4: Cleanup
1. **[CLEAN-01]** Hapus `resources/views/components/public/navbar.blade.php` dan `footer.blade.php`.
