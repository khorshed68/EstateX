<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyerReviewController extends Controller
{
    /**
     * Submit a review for a property.
     */
    public function storeProperty(Request $request)
    {
        $userId = session('buyer_user_id');
        
        $request->validate([
            'property_id' => 'required|integer',
            'rating' => 'required|numeric|min:1|max:5',
            'comments' => 'nullable|string|max:1000'
        ]);

        $propertyId = $request->input('property_id');
        $rating = $request->input('rating');
        $comments = $request->input('comments');

        // Check if already reviewed
        $existing = DB::select("SELECT id FROM reviews WHERE userId = :userId AND propertyId = :propertyId", [
            'userId' => $userId,
            'propertyId' => $propertyId
        ]);

        if (!empty($existing)) {
            return back()->with('error', 'You have already reviewed this property listing.');
        }

        try {
            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM reviews");
            $nextId = $nextIdResult[0]->next_id;

            DB::insert("
                INSERT INTO reviews (id, userId, propertyId, rating, comments) 
                VALUES (:id, :userId, :propertyId, :rating, :comments)
            ", [
                'id' => $nextId,
                'userId' => $userId,
                'propertyId' => $propertyId,
                'rating' => $rating,
                'comments' => $comments
            ]);

            return back()->with('success', 'Your property review has been submitted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error submitting review: ' . $e->getMessage());
        }
    }

    /**
     * Submit a review for an agent.
     */
    public function storeAgent(Request $request)
    {
        $userId = session('buyer_user_id');
        
        $request->validate([
            'agent_id' => 'required|integer',
            'rating' => 'required|numeric|min:1|max:5',
            'comments' => 'nullable|string|max:1000'
        ]);

        $agentId = $request->input('agent_id');
        $rating = $request->input('rating');
        $comments = $request->input('comments');

        // Check if already reviewed
        $existing = DB::select("SELECT id FROM agent_reviews WHERE userId = :userId AND agentId = :agentId", [
            'userId' => $userId,
            'agentId' => $agentId
        ]);

        if (!empty($existing)) {
            return back()->with('error', 'You have already submitted a rating for this agent.');
        }

        try {
            DB::beginTransaction();

            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM agent_reviews");
            $nextId = $nextIdResult[0]->next_id;

            DB::insert("
                INSERT INTO agent_reviews (id, userId, agentId, rating, comments) 
                VALUES (:id, :userId, :agentId, :rating, :comments)
            ", [
                'id' => $nextId,
                'userId' => $userId,
                'agentId' => $agentId,
                'rating' => $rating,
                'comments' => $comments
            ]);

            // Re-calculate agent's average rating using raw SQL aggregate
            $avgResult = DB::select("SELECT AVG(rating) AS avg_rating FROM agent_reviews WHERE agentId = :agentId", ['agentId' => $agentId]);
            $avgRating = !empty($avgResult) && $avgResult[0]->avg_rating !== null ? round($avgResult[0]->avg_rating, 2) : $rating;

            // Update agents table
            DB::statement("UPDATE agents SET rating = :rating WHERE id = :id", [
                'rating' => $avgRating,
                'id' => $agentId
            ]);

            DB::commit();

            return back()->with('success', 'Your agent rating has been submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error submitting rating: ' . $e->getMessage());
        }
    }
}
