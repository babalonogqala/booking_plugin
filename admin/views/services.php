<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="bm-wrap wrap">
    <h1 class="bm-title">
        <span class="dashicons dashicons-tag"></span>
        Services
        <button class="bm-btn bm-btn--primary" id="bm-add-service">+ Add Service</button>
    </h1>

    <div class="bm-card">
        <?php if ( empty( $services ) ) : ?>
            <div class="bm-empty">
                <span class="dashicons dashicons-tag"></span>
                <p>No services yet. Add your first service above.</p>
            </div>
        <?php else : ?>
        <table class="bm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Duration</th>
                    <th>Price (ZAR)</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $services as $s ) : ?>
                <tr data-service='<?php echo json_encode( $s ); ?>'>
                    <td><?php echo (int) $s->id; ?></td>
                    <td>
                        <strong><?php echo esc_html( $s->name ); ?></strong>
                        <?php if ( $s->description ) : ?>
                            <p class="bm-desc"><?php echo esc_html( $s->description ); ?></p>
                        <?php endif; ?>
                    </td>
                    <td><?php echo (int) $s->duration; ?> min</td>
                    <td>R <?php echo number_format( $s->price, 2 ); ?></td>
                    <td><?php echo (int) $s->capacity; ?></td>
                    <td>
                        <span class="bm-badge bm-badge--<?php echo $s->status === 'active' ? 'confirmed' : 'cancelled'; ?>">
                            <?php echo ucfirst( $s->status ); ?>
                        </span>
                    </td>
                    <td>
                        <div class="bm-actions">
                            <button class="bm-btn bm-btn--secondary bm-edit-service" data-id="<?php echo (int) $s->id; ?>">Edit</button>
                            <button class="bm-btn bm-btn--danger bm-delete-service" data-id="<?php echo (int) $s->id; ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Service Modal -->
<div id="bm-service-modal" class="bm-modal" style="display:none;">
    <div class="bm-modal__overlay"></div>
    <div class="bm-modal__box">
        <div class="bm-modal__header">
            <h2 id="bm-modal-title">Add Service</h2>
            <button class="bm-modal__close">&times;</button>
        </div>
        <div class="bm-modal__body">
            <input type="hidden" id="bm-service-id" value="">
            <div class="bm-field">
                <label>Service Name *</label>
                <input type="text" id="bm-svc-name" placeholder="e.g. Haircut, Consultation…">
            </div>
            <div class="bm-field">
                <label>Description</label>
                <textarea id="bm-svc-desc" rows="3" placeholder="Brief description of the service"></textarea>
            </div>
            <div class="bm-field-row">
                <div class="bm-field">
                    <label>Duration (minutes) *</label>
                    <input type="number" id="bm-svc-duration" value="60" min="15" step="15">
                </div>
                <div class="bm-field">
                    <label>Price (ZAR) *</label>
                    <input type="number" id="bm-svc-price" value="0.00" min="0" step="0.01">
                </div>
                <div class="bm-field">
                    <label>Capacity (slots)</label>
                    <input type="number" id="bm-svc-capacity" value="1" min="1">
                </div>
            </div>
            <div class="bm-field">
                <label>Status</label>
                <select id="bm-svc-status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="bm-modal__footer">
            <button class="bm-btn bm-btn--secondary bm-modal__close">Cancel</button>
            <button class="bm-btn bm-btn--primary" id="bm-save-service">Save Service</button>
        </div>
    </div>
</div>
